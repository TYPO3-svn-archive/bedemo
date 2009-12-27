<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

t3lib_extMgm::addPageTSConfig('
mod.tools_txbedemo {
	# deny changes in this branch for non-admin users
	deny = 0
	# deny changes in this branch for admins
	denyForAdmins = 0
}');

$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][$_EXTKEY] = 'EXT:' . $_EXTKEY . '/class.tx_bedemo_main.php:tx_bedemo_main';
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][$_EXTKEY] = 'EXT:' . $_EXTKEY . '/class.tx_bedemo_main.php:tx_bedemo_main';
?>