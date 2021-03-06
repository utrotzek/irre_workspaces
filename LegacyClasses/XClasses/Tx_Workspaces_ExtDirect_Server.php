<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2012 Oliver Hader <oliver.hader@typo3.org>
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license
 * from the author is found in LICENSE.txt distributed with these scripts.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * @author Oliver Hader <oliver.hader@typo3.org>
 * @package EXT:irre_workspaces
 */
class Ux_Tx_Workspaces_ExtDirect_Server extends tx_Workspaces_ExtDirect_Server {

	/**
	 * Fetches further information to current selected worspace record.
	 *
	 * @param object $parameter
	 * @return array $data
	 */
	public function getRowDetails($parameter) {
		$isTcaModified = FALSE;
		$isShowRecordFieldListModified = FALSE;

		$table = $parameter->table;

		/**
		 * Register a (fake) singleton instance to override t3lib_diff behaviours on rendering large FlexForms
		 * @var $differenceService Tx_IrreWorkspaces_Service_Difference_AlternativeCoreService
		 */
		$differenceService = t3lib_div::makeInstance('Tx_IrreWorkspaces_Service_Difference_AlternativeCoreService');
		$differenceService->setUseClearBuffer(FALSE);
		t3lib_div::setSingletonInstance('t3lib_diff', $differenceService);

		// Add sorting field to list of fields to be processed:
		t3lib_div::loadTCA($table);
		$sortingField = $this->getFieldDeviationService()->getTcaControlField($table, 'sortby');

		if (!empty($GLOBALS['TCA'][$table]['interface']['showRecordFieldList'])) {
			$showRecordFieldList = $GLOBALS['TCA'][$table]['interface']['showRecordFieldList'];
			unset($GLOBALS['TCA'][$table]['interface']['showRecordFieldList']);
		}

		if (empty($GLOBALS['TCA'][$table]['columns'][$sortingField])) {
			$GLOBALS['TCA'][$table]['columns'][$sortingField] = array(
				'label' => 'LLL:EXT:lang/locallang_core.xml:show_item.php.sorting',
				'config' => array(
					'type' => 'input',
					'eval' => 'int',
				),
			);
			$isTcaModified = TRUE;
		}

		$result = parent::getRowDetails($parameter);

		if ($isShowRecordFieldListModified) {
			$GLOBALS['TCA'][$table]['interface']['showRecordFieldList'] = $showRecordFieldList;
		}

		if ($isTcaModified) {
			unset($GLOBALS['TCA'][$table]['columns'][$sortingField]);
		}

		return $result;
	}

	/**
	 * @return Tx_IrreWorkspaces_Service_Field_DeviationService
	 */
	protected function getFieldDeviationService() {
		return t3lib_div::makeInstance('Tx_IrreWorkspaces_Service_Field_DeviationService');
	}

}

?>