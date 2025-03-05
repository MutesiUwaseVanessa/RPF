<?php
namespace LemmWerbeagentur\FluidStyledTeaser\Hooks;

use TYPO3\CMS\Backend\View\PageLayoutViewDrawItemHookInterface;
use TYPO3\CMS\Backend\View\PageLayoutView;

/*
 * This class contains a preview rendering for the page
 * module of CType="lemm_fluid_teaser"
 *
 * @author Sebastian Stockart <stockart@lemm.de>
 * @date 10/18/2017 09:42:00
 *
 */
class LemmTeaserPreviewRenderer implements PageLayoutViewDrawItemHookInterface
{
    /**
     * Preprocesses the preview rendering of a content element of type "lemm_fluid_teaser"
     *
     * @param \TYPO3\CMS\Backend\View\PageLayoutView $parentObject Calling parent object
     * @param bool $drawItem Whether to draw the item using the default functionality
     * @param string $headerContent Header content
     * @param string $itemContent Item content
     * @param array $row Record row of tt_content
     * @return void
     */
    public function preProcess(
        PageLayoutView &$parentObject,
        &$drawItem,
        &$headerContent,
        &$itemContent,
        array &$row
    ) {
        if ($row['CType'] === 'lemm_fluid_teaser') {
            $itemContent .= '<strong>Lemm Teaser</strong><br />';
            if ($row['assets']) {
                $itemContent .= $parentObject->thumbCode($row, 'tt_content', 'assets') . '<br />';
            }
            $drawItem = false;
        }
    }
}
