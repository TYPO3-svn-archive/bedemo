<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Steffen Gebert (steffen@steffen-gebert.de)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * TCEmain hooks checking for changes in demo branches of your site
 *
 * $Id$
 *
 * @author      Steffen Gebert <steffen@steffen-gebert.de>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 */

/**
 * TCEmain hooks checking for changes in demo branches of your site
 *
 * @author      Steffen Gebert <steffen@steffen-gebert.de>
 */
class tx_bedemo_main {

	private $extKey = 'bedemo';

	/**
	 * TCEmain hook to check for field modifications to existing records or creation of new records
	 * @param	array		Form fields.
	 * @param	array		Table the record belongs to.
	 * @param	integer		Id of the record.
	 * @param	object		Parent object.
	 * @return	void
	 */
	public function processDatamap_preProcessFieldArray(&$fieldArray, $table, $id, &$pObj) {
			// if a new record is created, we have only information about the target page
		if (substr($id, 0, 3) === 'NEW') {
			$table = 'pages';
			$id = $fieldArray['pid'];
		}

		if (!$this->checkAccess($table, $id)) {
			$fieldArray = NULL;
			$this->printMessage();
		}
	}

	/**
	 * TCEmain hook to take care of copy/move/delete/... commands
	 *
	 * @param	string		Command from tcemain.
	 * @param	string		Table the comman process on.
	 * @param	integer		Id of the record.
	 * @param	string		Value for the command.
	 * @param	object		Parent object.
	 * @return	void
	 */
	public function processCmdmap_preProcess(&$command, $table, $id, $value, &$pObj) {

		/**
		 * command                   | id                 | value
		 * ---------------------------------------------------------------------------
		 * copy                      | uid source         | pid destination (if >0) or
		 *                                                | record uid from the same table to be placed after (if <0)
		 * move                      | uid source         | see "copy"
		 * delete                    | uid to delete      | always 1
		 * undelete                  | uid to delete      | always 1
		 * localize                  | uid to translate   |
		 * inlineLocalizeSynchronize | uid                |
		 * version                   | array  @todo       |
		 */

		$checkRecord = array();

			// dependent on the $command we have to check different records
		switch ($command) {
			case 'move':
					// check the source record
				$checkRecord[] = array($table, $id);
					// and go on with the same checks as for "copy"
			case 'copy':
				if ($value > 0) {
						// we copy to a page
					$checkRecord[] = array('pages', $value);
				} elseif($value < 0) {
						// we copy $table:$uid to the same page as $table:$value
					$checkRecord[] = array($table, -$value);
				}
			break;

			case 'delete':
			case 'undelete':
			case 'translate':
			case 'inlineLocalizeSynchronize':
					// check the uid to process
				$checkRecord[] = array($table, $id);
			break;

			case 'version':
				// @todo here are more things to do, quite sure
				if ($value['swapWith']) {
					$checkRecord[] = array($table, $id);
				}
			break;
		}

		foreach ($checkRecord as $record) {
			if (!$this->checkAccess($record[0], $record[1])) {
				$command = NULL;
				$this->printMessage();
				break;
			}
		}
	}

	/**
	 * Checks, whether changes to $table:$id are allowed or not
	 *
	 * @param	string		database table
	 * @param	int		uid in database table
	 * @retun	boolean		TRUE, if changes allowed, FALSE otherwise
	 */
	private function checkAccess($table, $id) {
		$pageId = $this->getPageId($table, $id);

			// read out configuration vom PageTS mod.tools_bedemo.deny(ForAdmins)
		$tsConfig = t3lib_BEfunc::getModTSconfig($pageId, 'mod.tools_txbedemo');
		$deny = $tsConfig['properties']['deny'] === '1';
		$denyForAdmins = $tsConfig['properties']['denyForAdmins'] === '1';

		if (($deny && !$GLOBALS['BE_USER']->isAdmin()) || ($denyForAdmins && $GLOBALS['BE_USER']->isAdmin())) {
				// access denied
			return FALSE;
		} else {
				// change is allowed
			return TRUE;
		}
	}

	/**
	 * Prints a flashMessage to notify that the changes have been aborted
	 * @return	void
	 */
	private function printMessage() {
		$messageText = $GLOBALS['LANG']->sL('LLL:EXT:' . $this->extKey . '/locallang.xml:actionDenied.text');
		if ($GLOBALS['BE_USER']->isAdmin()) {
			$messageText .= '<br />' . $GLOBALS['LANG']->sL('LLL:EXT:' . $this->extKey . '/locallang.xml:actionDenied.admin');
		}

		$flashMessage = t3lib_div::makeInstance(
			't3lib_FlashMessage',
			$messageText,
			$text = $GLOBALS['LANG']->sL('LLL:EXT:' . $this->extKey . '/locallang.xml:actionDenied.header'),
			t3lib_FlashMessage::ERROR,
			TRUE // store in session
		);
		t3lib_FlashMessageQueue::addMessage($flashMessage);
	}

	/**
	 * Retrieves the nearest pid for the record $table:$id
	 *
	 * @param	string		database table
	 * @param	int		uid in database table
	 * @return	int		$id, if $table == pages, otherwise the pid of $table:$id
	 */
	private function getPageId($table, $id) {
		$pageId = 0;
		if ($table == 'pages') {
			$pageId = $id;
		} else {
			$tce = t3lib_div::makeInstance('t3lib_TCEmain');
			$pageInfo = $tce->recordInfo($table, $id, 'pid');
			$pageId = $pageInfo['pid'];
		}
		return $pageId;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/bedemo/class.tx_bedemo_main.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/bedemo/class.tx_bedemo_main.php']);
}
?>