<?php
defined('TYPO3_MODE') or die();

call_user_func(function () {

    $languageFilePrefix = 'LLL:EXT:fluid_styled_content/Resources/Private/Language/Database.xlf:';
    $customLanguageFilePrefix = 'LLL:EXT:lemm_fluid_teaser/Resources/Private/Language/locallang_be.xlf:';
    $frontendLanguageFilePrefix = 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:';

    // Add the CType "lw_teaser"
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
        'tt_content',
        'CType',
        [
            'LLL:EXT:lemm_fluid_teaser/Resources/Private/Language/locallang_be.xlf:wizard.title',
            'lemm_fluid_teaser',
            'content-image'
        ],
        'textmedia',
        'after'
    );

    $GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['lemm_fluid_teaser'] = $GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['textmedia'];

    // Define what fields to display
    $GLOBALS['TCA']['tt_content']['types']['lemm_fluid_teaser'] = [
        'showitem'         => '
                --palette--;' . $frontendLanguageFilePrefix . 'palette.general;general,
                --palette--;' . $languageFilePrefix . 'tt_content.palette.mediaAdjustments;mediaAdjustments,
                pi_flexform,
            --div--;' . $customLanguageFilePrefix . 'tca.tab.teaserElements,
                 assets
        ',
        'columnsOverrides' => [
            'media' => [
                'label'  => $languageFilePrefix . 'tt_content.media_references',
                'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('media', [
                    'appearance'    => [
                        'createNewRelationLinkTitle' => $languageFilePrefix . 'tt_content.media_references.addFileReference'
                    ],
                    // custom configuration for displaying fields in the overlay/reference table
                    // behaves the same as the image field.
                    'foreign_types' => $GLOBALS['TCA']['tt_content']['columns']['image']['config']['foreign_types']
                ], $GLOBALS['TYPO3_CONF_VARS']['SYS']['mediafile_ext'])
            ]
        ]
    ];

    // Add a flexform to the lw_teaser CType
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
        '',
        'FILE:EXT:lemm_fluid_teaser/Configuration/FlexForms/lw_teaser_flexform.xml',
        'lemm_fluid_teaser'
    );

});