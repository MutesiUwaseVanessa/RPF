<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace TYPO3\CMS\Core\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\Configuration\DispositionConfiguration;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\Configuration\DispositionMapFactory;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\PolicyProvider;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\Reporting\Report;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\Reporting\ReportDetails;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\Reporting\ReportRepository;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\Reporting\ReportStatus;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\Scope;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\IpAnonymizationUtility;

/**
 * @internal
 */
abstract class AbstractContentSecurityPolicyReporter implements MiddlewareInterface
{
    protected const URI_KEYS = ['document-uri', 'report-uri', 'blocked-uri', 'referrer'];

    public function __construct(
        protected readonly PolicyProvider $policyProvider,
        protected readonly DispositionMapFactory $dispositionMapFactory,
        protected readonly ReportRepository $reportRepository
    ) {}

    protected function persistCspReport(Scope $scope, ServerRequestInterface $request): void
    {
        $payload = (string)$request->getBody();
        if (!$this->isJson($payload)) {
            return;
        }
        $normalizedParams = $request->getAttribute('normalizedParams');
        $meta = [
            'addr' => IpAnonymizationUtility::anonymizeIp($normalizedParams->getRemoteAddress()),
            'agent' => $normalizedParams->getHttpUserAgent(),
        ];
        // skip potential externally injected violation reports
        $requestTime = $this->getRequestQueryParam($request, 'requestTime');
        $requestHash = $this->getRequestQueryParam($request, 'requestHash');
        if ($requestTime === null
            || $requestHash === null
            || !$this->validateHmac($requestTime, $requestHash)
        ) {
            return;
        }
        $originalDetails = json_decode($payload, true)['csp-report'] ?? [];
        $originalDetails = $this->anonymizeDetails($originalDetails);
        $details = new ReportDetails($originalDetails);
        $summary = $this->generateReportSummary($scope, $details);
        $report = new Report(
            $scope,
            ReportStatus::New,
            (int)$requestTime,
            $meta,
            $details,
            $summary
        );
        $this->reportRepository->add($report);
    }

    protected function generateReportSummary(Scope $scope, ReportDetails $details): string
    {
        return GeneralUtility::hmac(
            json_encode([
                $scope,
                $details['effective-directive'],
                $details['blocked-uri'],
                $details['script-sample'] ?? null,
            ]),
            self::class,
        );
    }

    protected function anonymizeDetails(array $details): array
    {
        foreach (self::URI_KEYS as $uriKey) {
            if (!isset($details[$uriKey])) {
                continue;
            }
            $details[$uriKey] = $this->anonymizeUri($details[$uriKey]);
        }
        return $details;
    }

    protected function anonymizeUri(string $value): string
    {
        try {
            $uri = Uri::fromAnyScheme($value);
        } catch (\Throwable) {
            return '';
        }
        if ($uri->getQuery() === '') {
            return $value;
        }
        parse_str($uri->getQuery(), $query);
        // strip CSRF token (might be a different usage as well)
        unset($query['token']);
        return (string)$uri->withQuery(http_build_query($query, '', '&', PHP_QUERY_RFC3986));
    }

    /**
     * Determines, whether current request URI starts with local reporting URI,
     * (e.g. `https://ip12.anyhost.it:8443/en/@http-reporting?csp=report`).
     */
    protected function targetsCspReportUri(Scope $scope, ServerRequestInterface $request): bool
    {
        $normalizedParams = $request->getAttribute('normalizedParams');
        $reportingUriBase = $this->policyProvider->getDefaultReportingUriBase($scope, $request, false);
        return str_starts_with($normalizedParams?->getRequestUri() ?? '', (string)$reportingUriBase);
    }

    /**
     * Determines, whether the request is eligible to be handled by the local reporting URI
     * (`targetsCspReportUri()` must have been called before).
     */
    protected function isCspReport(
        Scope $scope,
        ServerRequestInterface $request,
        ?DispositionConfiguration $dispositionConfiguration = null,
    ): bool {
        // @todo
        // + verify current session
        // + invoke rate limiter
        // + check additional scope (snippet enrichment)

        $reportingUrl = $this->policyProvider->getReportingUrlFor($scope, $request, $dispositionConfiguration);
        $reportingUriBase = $this->policyProvider->getDefaultReportingUriBase($scope, $request);
        $contentTypeHeader = $request->getHeaderLine('content-type');

        return
            // stop, if reporting was explicitly disabled in `contentSecurityPolicyReportingUrl`
            $reportingUrl !== null
            // stop, if different reporting URI was configured in `contentSecurityPolicyReportingUrl`
            && str_starts_with((string)$reportingUrl, (string)$reportingUriBase)
            // stop, if request is not `POST` or `Content-Type` is not `application/csp-report`
            && $request->getMethod() === 'POST' && $contentTypeHeader === 'application/csp-report';
    }

    protected function getRequestQueryParam(ServerRequestInterface $request, string $name): ?string
    {
        $value = $request->getQueryParams()[$name] ?? null;
        return is_string($value) ? $value : null;
    }

    protected function validateHmac(string $string, string $givenHmac): bool
    {
        $expectedHmac = GeneralUtility::hmac($string, self::class);
        return hash_equals($expectedHmac, $givenHmac);
    }

    protected function isJson(string $value): bool
    {
        try {
            json_decode($value, false, 16, JSON_THROW_ON_ERROR);
            return true;
        } catch (\JsonException) {
            return false;
        }
    }
}
