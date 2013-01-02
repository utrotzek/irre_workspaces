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
class ux_tx_version_tcemain extends tx_version_tcemain {
	/**
	 * Send an email notification to users in workspace
	 *
	 * @param array $stat Workspace access array (from t3lib_userauthgroup::checkWorkspace())
	 * @param integer $stageId New Stage number: 0 = editing, 1= just ready for review, 10 = ready for publication, -1 = rejected!
	 * @param string $table Table name of element (or list of element names if $id is zero)
	 * @param integer $id Record uid of element (if zero, then $table is used as reference to element(s) alone)
	 * @param string $comment User comment sent along with action
	 * @param t3lib_TCEmain $tcemainObj TCEmain object
	 * @param array $notificationAlternativeRecipients List of recipients to notify instead of be_users selected by sys_workspace, list is generated by workspace extension module
	 * @return void
	 */
	protected function notifyStageChange(array $stat, $stageId, $table, $id, $comment, t3lib_TCEmain $tcemainObj, array $notificationAlternativeRecipients = array()) {
		$this->getChangeStageActionService()->notify(
			$stat,
			$stageId,
			$table,
			$id,
			$comment,
			$notificationAlternativeRecipients
		);
	}

	/**
	 * Release version from this workspace (and into "Live" workspace but as an offline version).
	 *
	 * @param string $table Table name
	 * @param integer $id Record UID
 	 * @param boolean $flush If set, will completely delete element
	 * @param t3lib_TCEmain $tcemainObj TCEmain object
	 * @return void
	 */
	public function invokeParentClearWSID($table, $id, $flush = FALSE, t3lib_TCEmain $tcemainObj) {
		parent::version_clearWSID($table, $id, $flush, $tcemainObj);
	}

	/**
	 * Release version from this workspace (and into "Live" workspace but as an offline version).
	 *
	 * @param string $table Table name
	 * @param integer $id Record UID
 	 * @param boolean $flush If set, will completely delete element
	 * @param t3lib_TCEmain $tcemainObj TCEmain object
	 * @return void
	 */
	protected function version_clearWSID($table, $id, $flush = FALSE, t3lib_TCEmain $tcemainObj) {
		if ($errorCode = $tcemainObj->BE_USER->workspaceCannotEditOfflineVersion($table, $id)) {
			$tcemainObj->newlog('Attempt to reset workspace for record failed: ' . $errorCode, 1);
		} elseif ($tcemainObj->checkRecordUpdateAccess($table, $id)) {
			$this->getFlushWorkspaceActionService()->clearElement($table, $id, $flush, $tcemainObj, $this);
		}
	}

	/**
	 * @return Tx_IrreWorkspaces_Service_Action_ChangeStageActionService
	 */
	protected function getChangeStageActionService() {
		return t3lib_div::makeInstance('Tx_IrreWorkspaces_Service_Action_ChangeStageActionService');
	}

	/**
	 * @return Tx_IrreWorkspaces_Service_Action_FlushWorkspaceActionService
	 */
	protected function getFlushWorkspaceActionService() {
		return t3lib_div::makeInstance('Tx_IrreWorkspaces_Service_Action_FlushWorkspaceActionService');
	}
}

?>