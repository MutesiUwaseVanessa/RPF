<?php
defined('TYPO3_MODE') or die();

if (TYPO3_MODE === 'BE') {
    // Include new content elements to modWizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:lemm_fluid_teaser/Configuration/PageTSconfig/FluidStyledTeaser.ts">'
    );

    // Register hook to show preview of tt_content element of CType="fluid_styled_teaser" in page module
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem']['lemm_fluid_teaser']
        = \LemmWerbeagentur\FluidStyledTeaser\Hooks\LemmTeaserPreviewRenderer::class;
}
