<?php

declare(strict_types=1);

namespace GridElementsTeam\Gridelements\Wizard;

/***************************************************************
 *  Copyright notice
 *  (c) 2013 Jo Hasenau <info@cybercraft.de>
 *  All rights reserved
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use GridElementsTeam\Gridelements\Backend\LayoutSetup;
use TYPO3\CMS\Backend\Form\Element\BackendLayoutWizardElement;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\TypoScript\TypoScriptStringFactory;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Script Class for grid wizard
 */
class GridelementsBackendLayoutWizardElement extends BackendLayoutWizardElement
{
    /**
     * @var array
     */
    protected array $rows = [];

    /**
     * @var int
     */
    protected int $colCount = 0;

    /**
     * @var int
     */
    protected int $rowCount = 0;

    /**
     * @return array
     */
    public function render(): array
    {
        $lang = $this->getLanguageService();
        $resultArray = $this->initializeResultArray();
        $this->init();

        // readOnly is not supported as columns config but might be set by SingleFieldContainer in case
        // "l10n_display" is set to "defaultAsReadonly". To prevent misbehaviour for fields, which falsely
        // set this, we also check for "defaultAsReadonly" being set and whether the record is an overlay.
        $readOnly = ($parameterArray['fieldConf']['config']['readOnly'] ?? false)
            && ($tca['ctrl']['transOrigPointerField'] ?? false)
            && ($row[$tca['ctrl']['transOrigPointerField']][0] ?? $row[$tca['ctrl']['transOrigPointerField']] ?? false)
            && GeneralUtility::inList($parameterArray['fieldConf']['l10n_display'] ?? '', 'defaultAsReadonly');

        $fieldInformationResult = $this->renderFieldInformation();
        $fieldInformationHtml = $fieldInformationResult['html'];
        $resultArray = $this->mergeChildReturnIntoExistingResult($resultArray, $fieldInformationResult, false);

        // Use CodeMirror if available
        if (ExtensionManagementUtility::isLoaded('t3editor')) {
            $codeMirrorConfig = [
                    'label' => $lang->sL('LLL:EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf:buttons.pageTsConfig'),
                    'panel' => 'top',
                    'mode' => GeneralUtility::jsonEncodeForHtmlAttribute(JavaScriptModuleInstruction::create('@typo3/t3editor/language/typoscript.js', 'typoscript')->invoke(), false),
                    'nolazyload' => 'true',
                    'readonly' => 'true',
            ];
            $editor = '
                <typo3-t3editor-codemirror class="t3js-grideditor-preview-config grideditor-preview" ' . GeneralUtility::implodeAttributes($codeMirrorConfig, true) . '>
                    <textarea class="t3js-tsconfig-preview-area form-control"></textarea>
                </typo3-t3editor-codemirror>';

            $resultArray['javaScriptModules'][] = JavaScriptModuleInstruction::create('@typo3/t3editor/element/code-mirror-element.js');
        } else {
            $editor = '
                <label>' . htmlspecialchars($lang->sL('LLL:EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf:buttons.pageTsConfig')) . '</label>
                <div class="t3js-grideditor-preview-config grideditor-preview">
                    <textarea class="t3js-tsconfig-preview-area form-control" rows="25" readonly></textarea>
                </div>';
        }

        $json = (string)json_encode($this->rows, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
        $html = [];
        $html[] = '<div class="formengine-field-item t3js-formengine-field-item">';
        $html[] =   $fieldInformationHtml;
        $html[] =   '<div class="form-control-wrap">';
        $html[] =       '<div class="form-wizards-wrap">';
        $html[] =           '<div class="form-wizards-element">';
        $html[] =               '<input';
        $html[] =                   ' type="hidden"';
        $html[] =                   ' name="' . htmlspecialchars($this->data['parameterArray']['itemFormElName']) . '"';
        $html[] =                   ' value="' . htmlspecialchars($this->data['parameterArray']['itemFormElValue']) . '"';
        $html[] =                   '/>';
        $html[] =               '<div class="grideditor' . ($readOnly ? ' grideditor-readonly' : '') . '">';
        if (!$readOnly) {
            $html[] =               '<div class="grideditor-control grideditor-control-top">';
            $html[] =                   '<div class="btn-group">';
            $html[] =                       '<a class="btn btn-default btn-sm t3js-grideditor-addrow-top" href="#"';
            $html[] =                           ' title="' . htmlspecialchars($lang->sL('LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:grid_addRow')) . '">';
            $html[] =                           $this->iconFactory->getIcon('actions-plus', Icon::SIZE_SMALL)->render();
            $html[] =                       '</a>';
            $html[] =                       '<a class="btn btn-default btn-sm t3js-grideditor-removerow-top" href="#"';
            $html[] =                           ' title="' . htmlspecialchars($lang->sL('LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:grid_removeRow')) . '">';
            $html[] =                           $this->iconFactory->getIcon('actions-minus', Icon::SIZE_SMALL)->render();
            $html[] =                       '</a>';
            $html[] =                   '</div>';
            $html[] =               '</div>';
        }
        $html[] =                   '<div class="grideditor-editor">';
        $html[] =                       '<div';
        $html[] =                           ' id="editor"';
        $html[] =                           ' class="t3js-grideditor"';
        $html[] =                           ' data-data="' . htmlspecialchars($json) . '"';
        $html[] =                           ' data-rowcount="' . (int)$this->rowCount . '"';
        $html[] =                           ' data-colcount="' . (int)$this->colCount . '"';
        $html[] =                           ' data-readonly="' . ($readOnly ? '1' : '0') . '"';
        $html[] =                           ' data-field="' . htmlspecialchars($this->data['parameterArray']['itemFormElName']) . '"';
        $html[] =                       '></div>';
        $html[] =                   '</div>';
        if (!$readOnly) {
            $html[] =               '<div class="grideditor-control grideditor-control-right">';
            $html[] =                   '<div class="btn-group-vertical">';
            $html[] =                       '<a class="btn btn-default btn-sm t3js-grideditor-addcolumn" href="#"';
            $html[] =                           ' title="' . htmlspecialchars($lang->sL('LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:grid_addColumn')) . '">';
            $html[] =                           $this->iconFactory->getIcon('actions-plus', Icon::SIZE_SMALL)->render();
            $html[] =                       '</a>';
            $html[] =                       '<a class="btn btn-default btn-sm t3js-grideditor-removecolumn" href="#"';
            $html[] =                           ' title="' . htmlspecialchars($lang->sL('LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:grid_removeColumn')) . '">';
            $html[] =                           $this->iconFactory->getIcon('actions-minus', Icon::SIZE_SMALL)->render();
            $html[] =                       '</a>';
            $html[] =                   '</div>';
            $html[] =               '</div>';
            $html[] =               '<div class="grideditor-control grideditor-control-bottom">';
            $html[] =                   '<div class="btn-group">';
            $html[] =                       '<a class="btn btn-default btn-sm t3js-grideditor-addrow-bottom" href="#"';
            $html[] =                           ' title="' . htmlspecialchars($lang->sL('LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:grid_addRow')) . '">';
            $html[] =                           $this->iconFactory->getIcon('actions-plus', Icon::SIZE_SMALL)->render();
            $html[] =                       '</a>';
            $html[] =                       '<a class="btn btn-default btn-sm t3js-grideditor-removerow-bottom" href="#"';
            $html[] =                           ' title="' . htmlspecialchars($lang->sL('LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:grid_removeRow')) . '">';
            $html[] =                           $this->iconFactory->getIcon('actions-minus', Icon::SIZE_SMALL)->render();
            $html[] =                       '</a>';
            $html[] =                   '</div>';
            $html[] =               '</div>';
        }
        $html[] =                   '<div class="grideditor-preview">' . $editor . '</div>';
        $html[] =                '</div>';
        if (!$readOnly && !empty($fieldWizardHtml)) {
            $html[] =           '<div class="form-wizards-items-bottom">' . $fieldWizardHtml . '</div>';
        }
        $html[] =           '</div>';
        $html[] =       '</div>';
        $html[] =   '</div>';
        $html[] = '</div>';

        $contentTypes = [];
        if (is_array($GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'])) {
            foreach ($GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'] as $item) {
                $contentType = [];
                if (!empty($item['value'])) {
                    $contentType['key'] = $item['value'];
                    if (substr((string)$contentType['key'], 0, 2) !== '--') {
                        $contentType['label'] = $lang->sL($item['label']);
                        $contentTypes[] = $contentType;
                    }
                }
            }
        }
        $listTypes = [];
        if (is_array($GLOBALS['TCA']['tt_content']['columns']['list_type']['config']['items'])) {
            foreach ($GLOBALS['TCA']['tt_content']['columns']['list_type']['config']['items'] as $item) {
                $listType = [];
                if (!empty($item['value'])) {
                    $listType['key'] = $item['value'];
                    if (substr((string)$listType['key'], 0, 2) !== '--') {
                        $listType['label'] = $lang->sL($item['label']);
                        $listTypes[] = $listType;
                    }
                }
            }
        }
        $gridTypes = [];
        $layoutSetup = GeneralUtility::makeInstance(LayoutSetup::class)->init($this->data['parentPageRow']['pid'] ?? 0)->getLayoutSetup();
        if (is_array($layoutSetup)) {
            foreach ($layoutSetup as $key => $item) {
                $gridType = [];
                if (!empty($key)) {
                    $gridType['key'] = $key;
                    if (substr((string)$gridType['key'], 0, 2) !== '--') {
                        $gridType['label'] = $lang->sL($item['title']);
                        $gridTypes[] = $gridType;
                    }
                }
            }
        }
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->addJsInlineCode(
                'GridelementsBackendLayout',
                'var Gridelements = Gridelements || {}; Gridelements.BackendLayout = Gridelements.BackendLayout || {}; ' .
                ($contentTypes ? ' Gridelements.BackendLayout.availableCTypes = ' . json_encode($contentTypes) . '; ' : '') .
                ($listTypes ? '  Gridelements.BackendLayout.availableListTypes = ' . json_encode($listTypes) . '; ' : '') .
                ($gridTypes ? '  Gridelements.BackendLayout.availableGridTypes = ' . json_encode($gridTypes) . '; ' : ''),
                true,false,true
        );
        $html = implode(LF, $html);
        $resultArray['html'] = $this->wrapWithFieldsetAndLegend($html);
        $resultArray['javaScriptModules'][] = JavaScriptModuleInstruction::create(
            '@typo3/backend/gridelements/grid-editor.js',
            'GridEditor'
        )->instance();
        $resultArray['additionalInlineLanguageLabelFiles'][] = 'EXT:core/Resources/Private/Language/locallang_wizards.xlf';
        $resultArray['additionalInlineLanguageLabelFiles'][] = 'EXT:backend/Resources/Private/Language/locallang.xlf';
        $resultArray['additionalInlineLanguageLabelFiles'][] = 'EXT:gridelements/Resources/Private/Language/locallang_wizard.xlf';

        return $resultArray;
    }

    /**
     * Initialize wizard
     */
    protected function init()
    {
        if (empty($this->data['databaseRow']['config'])) {
            $rows = [[['colspan' => 1, 'rowspan' => 1, 'spanned' => 0, 'name' => '0x0']]];
            $colCount = 1;
            $rowCount = 1;
        } else {
            /** @var LayoutSetup $layoutSetup */
            $layoutSetup = GeneralUtility::makeInstance(LayoutSetup::class)->init(0);
            if ($this->data['tableName'] === 'tx-gridelements_backend_layout') {
                $layoutId = $this->data['databaseRow']['alias'] ?? (int)$this->data['databaseRow']['uid'];
                $layout = $layoutSetup->getLayoutSetup($layoutId);
            } else {
                /* @var TypoScriptStringFactory $parser */
                $parser = GeneralUtility::makeInstance(TypoScriptStringFactory::class);
                $typoScriptSetup = $parser->parseFromStringWithIncludes(
                    'gridelements-typoscript-config-' . (int)$this->data['databaseRow']['uid'],
                    $this->data['databaseRow']['config']
                )->toArray();

                $layout = ['config' => $typoScriptSetup['backend_layout.']];

                if (!empty($layout['config']['rows.'])) {
                    $columns = $layoutSetup->checkAvailableColumns($layout);
                    if (!empty($columns['allowed']) || !empty($columns['disallowed']) || !empty($columns['maxitems'])) {
                        $layout['columns'] = $columns;
                        unset($layout['columns']['allowed']);
                        $layout['allowed'] = $columns['allowed'] ?? [];
                        $layout['disallowed'] = $columns['disallowed'] ?? [];
                        $layout['maxitems'] = $columns['maxitems'] ?? [];
                    }
                }
            }
            $rows = [];
            $colCount = $layout['config']['colCount'] ?? 0;
            $rowCount = $layout['config']['rowCount'] ?? 0;
            $dataRows = $layout['config']['rows.'] ?? [];
            $spannedMatrix = [];
            for ($i = 1; $i <= $rowCount; $i++) {
                $cells = [];
                $row = array_shift($dataRows);
                if (empty($row['columns.'])) {
                    continue;
                }
                $columns = $row['columns.'];
                for ($j = 1; $j <= $colCount; $j++) {
                    $cellData = [];
                    if (empty($spannedMatrix[$i][$j])) {
                        if (!empty($columns) && is_array($columns)) {
                            $column = array_shift($columns);
                            if (isset($column['colspan'])) {
                                $cellData['colspan'] = (int)$column['colspan'];
                                $columnColSpan = (int)$column['colspan'];
                                if (isset($column['rowspan'])) {
                                    $columnRowSpan = (int)$column['rowspan'];
                                    for ($spanRow = 0; $spanRow < $columnRowSpan; $spanRow++) {
                                        for ($spanColumn = 0; $spanColumn < $columnColSpan; $spanColumn++) {
                                            if (!isset($spannedMatrix[$i + $spanRow])) {
                                                $spannedMatrix[$i + $spanRow] = [];
                                            }
                                            $spannedMatrix[$i + $spanRow][$j + $spanColumn] = 1;
                                        }
                                    }
                                } else {
                                    for ($spanColumn = 0; $spanColumn < $columnColSpan; $spanColumn++) {
                                        if (!isset($spannedMatrix[$i])) {
                                            $spannedMatrix[$i] = [];
                                        }
                                        $spannedMatrix[$i][$j + $spanColumn] = 1;
                                    }
                                }
                            } else {
                                $cellData['colspan'] = 1;
                                if (isset($column['rowspan'])) {
                                    $columnRowSpan = (int)$column['rowspan'];
                                    for ($spanRow = 0; $spanRow < $columnRowSpan; $spanRow++) {
                                        if (!isset($spannedMatrix[$i + $spanRow])) {
                                            $spannedMatrix[$i + $spanRow] = [];
                                        }
                                        $spannedMatrix[$i + $spanRow][$j] = 1;
                                    }
                                }
                            }
                            if (isset($column['rowspan'])) {
                                $cellData['rowspan'] = (int)$column['rowspan'];
                            } else {
                                $cellData['rowspan'] = 1;
                            }
                            if (isset($column['name'])) {
                                $cellData['name'] = $column['name'];
                            }
                            if (isset($column['colPos'])) {
                                $colPos = (int)$column['colPos'];
                                $cellData['column'] = $colPos;
                                $cellData['allowed'] = [];
                                if (isset($layout['allowed'][$colPos])) {
                                    foreach ($layout['allowed'][$colPos] as $key => $valueArray) {
                                        $cellData['allowed'][$key] = implode(',', array_keys($valueArray));
                                    }
                                }
                                $cellData['disallowed'] = [];
                                if (isset($layout['disallowed'][$colPos])) {
                                    foreach ($layout['disallowed'][$colPos] as $key => $valueArray) {
                                        $cellData['disallowed'][$key] = implode(',', array_keys($valueArray));
                                    }
                                }
                                if (isset($layout['maxitems'][$colPos])) {
                                    $cellData['maxitems'] = (int)$layout['maxitems'][$colPos];
                                }
                            }
                        }
                    } else {
                        $cellData = ['colspan' => 1, 'rowspan' => 1, 'spanned' => 1];
                    }
                    $cells[] = $cellData;
                }
                $rows[] = $cells;
                if (!empty($spannedMatrix[$i]) && is_array($spannedMatrix[$i])) {
                    ksort($spannedMatrix[$i]);
                }
            }
        }
        $this->rows = $rows;
        $this->colCount = (int)$colCount;
        $this->rowCount = (int)$rowCount;
    }
}
