<?php

declare(strict_types=1);

namespace GridElementsTeam\Gridelements\View\BackendLayout\Grid;

use GridElementsTeam\Gridelements\Helper\GridElementsHelper;
use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\BackendLayout\Grid\GridColumnItem;
use TYPO3\CMS\Backend\View\PageLayoutContext;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Grid Column Item
 *
 * Model/proxy around a single record which appears in a grid column
 * in the page layout. Returns titles, urls etc. and performs basic
 * assertions on the contained content element record such as
 * is-versioned, is-editable, is-delible and so on.
 *
 * Accessed from Fluid templates.
 *
 * @internal this is experimental and subject to change in TYPO3 v10 / v11
 */
class GridelementsGridColumnItem extends GridColumnItem
{
    /**
     * @var array
     */
    protected array $layoutColumns;

    /**
     * @param PageLayoutContext $context
     * @param GridelementsGridColumn $column
     * @param array $record
     * @param string $table
     * @param array $layoutColumns
     */
    public function __construct(PageLayoutContext $context, GridelementsGridColumn $column, array $record, string $table = 'tt_content', array $layoutColumns = [])
    {
        parent::__construct($context, $column, $record, $table);
        $this->layoutColumns = $layoutColumns;
    }

    /**
     * @return GridelementsGridColumn
     */
    public function getGridelementsColumn(): GridelementsGridColumn
    {
        return $this->column;
    }

    /**
     * @return string
     */
    public function getWrapperClassName(): string
    {
        $wrapperClassNames = [];
        if ($this->isDisabled()) {
            $wrapperClassNames[] = 't3-page-ce-hidden t3js-hidden-record';
        } elseif ($this->record['colPos'] !== -1) {
            $wrapperClassNames[] = 't3-page-ce-warning';
        } elseif (!GeneralUtility::inList($this->layoutColumns['CSV'] ?? '', $this->record['tx_gridelements_columns'])) {
            $wrapperClassNames[] = 't3-page-ce-warning';
        } else {
            $this->getGridelementsColumn()->setActive();
        }
        if ($this->isInconsistentLanguage()) {
            $wrapperClassNames[] = 't3-page-ce-danger';
        }

        return implode(' ', $wrapperClassNames);
    }

    public function isInconsistentLanguage(): bool
    {
        $allowInconsistentLanguageHandling = (bool)(BackendUtility::getPagesTSconfig($this->getContext()->getPageId())['mod.']['web_layout.']['allowInconsistentLanguageHandling'] ?? false);
        return !$allowInconsistentLanguageHandling
            && $this->getSiteLanguage()->getLanguageId() !== 0
            && $this->getContext()->getLanguageModeIdentifier() === 'mixed'
            && (int)$this->record['l18n_parent'] === 0;
    }

    /**
     * @return string
     * @throws RouteNotFoundException
     */
    public function getNewContentAfterUrlWithRestrictions(): string
    {
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $pageId = $this->context->getPageId();

        $specificIds = GridElementsHelper::getSpecificIds($this->record);
        $allowed = base64_encode(json_encode($this->column->getAllowed()));
        $disallowed = base64_encode(json_encode($this->column->getDisallowed()));

        $urlParameters = [
            'id' => $pageId,
            'sys_language_uid' => $this->context->getSiteLanguage()->getLanguageId(),
            'tx_gridelements_allowed' => $allowed,
            'tx_gridelements_disallowed' => $disallowed,
            'tx_gridelements_container' => $this->column->getGridContainerId(),
            'tx_gridelements_columns' => $this->column->getColumnNumber(),
            'colPos' => -1,
            'uid_pid' => -$this->record['uid'],
            'returnUrl' => GeneralUtility::getIndpEnv('REQUEST_URI'),

        ];
        $routeName = 'new_content_element_wizard';

        return (string)$uriBuilder->buildUriFromRoute($routeName, $urlParameters);
    }
}
