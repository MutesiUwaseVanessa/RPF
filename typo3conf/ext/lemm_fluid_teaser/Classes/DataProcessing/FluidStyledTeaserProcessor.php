<?php
namespace LemmWerbeagentur\FluidStyledTeaser\DataProcessing;

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\FlexFormService;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/*
 * This class processes the input data for the teaser element and
 * injects it to the fluid template.
 *
 * @author Sebastian Stockart <stockart@lemm.de>
 * @date 10/18/2017 09:42:00
 *
 */
class FluidStyledTeaserProcessor implements DataProcessorInterface {

	/**
	 * Process data for the content element "teaser" and returns all variables
	 * to the fluid template / partial.
	 *
	 * @param ContentObjectRenderer $cObj The data of the content element or page
	 * @param array $contentObjectConfiguration The configuration of Content Object
	 * @param array $processorConfiguration The configuration of this processor
	 * @param array $processedData Key/value store of processed data (e.g. to be passed to a Fluid View)
	 * @return array the processed data as key/value store
	 */
	public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration, array $processedData) {
		$processedData['options'] = $this->getOptionsFromFlexFormData($processedData['data']);
		return $processedData;
	}

	/**
	 * Returns the options set for this content element in the admin
	 * panel.
	 *
	 * @param array $row
	 * @return array
	 */
	protected function getOptionsFromFlexFormData(array $row)
	{
		$options = [];
		$flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
		$flexFormAsArray = $flexFormService->convertFlexFormContentToArray($row['pi_flexform']);
		foreach ($flexFormAsArray['options'] as $optionKey => $optionValue) {
			switch ($optionValue) {
				case '1':
					$options[$optionKey] = true;
					break;
				case '0':
					$options[$optionKey] = false;
					break;
				default:
					$options[$optionKey] = $optionValue;
			}
		}
		return $options;
	}
}
