<?php

########################################################################
# Extension Manager/Repository config file for ext "bedemo".
#
# Auto generated 27-12-2009 16:01
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Backend demonstration',
	'description' => 'EXT:bedemo allows your editors to use the have a look at the configuration of pages and records TYPO3 Backend, but prevents them to save any changes.
This is helpfull, if you e.g. have a demo site in your installation, for which your editors can see the FE output and should should be able to see, how the pages are configured in BE.',
	'category' => 'be',
	'author' => 'Steffen Gebert',
	'author_email' => 'steffen@steffen-gebert.de',
	'shy' => '',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'doNotLoadInFE' => 1,
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.1.0',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.3.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:8:{s:9:"ChangeLog";s:4:"e326";s:24:"class.tx_bedemo_main.php";s:4:"d0eb";s:12:"ext_icon.gif";s:4:"fdbd";s:17:"ext_localconf.php";s:4:"cd72";s:13:"locallang.xml";s:4:"5053";s:14:"doc/manual.sxw";s:4:"0b13";s:19:"doc/wizard_form.dat";s:4:"8334";s:20:"doc/wizard_form.html";s:4:"aeca";}',
	'suggests' => array(
	),
);

?>