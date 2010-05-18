<?php

########################################################################
# Extension Manager/Repository config file for ext "stoefln_pear_mail".
#
# Auto generated 18-05-2010 11:13
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Pear mail for TYPO3',
	'description' => 'Sends mails over SMTP server with authentication. XCLASS extension for t3lib_htmlmail. Sends mails with pear mail lib instead of PHP\'s mail() function',
	'category' => 'misc',
	'shy' => 0,
	'version' => '0.1.0',
	'dependencies' => 'cms',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Stephan Petzl, Axel Klarmann',
	'author_email' => 'spetzl@gmx.at',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:10:{s:9:"ChangeLog";s:4:"04d2";s:10:"README.txt";s:4:"ee2d";s:22:"class.ux_t3lib_div.php";s:4:"5f49";s:27:"class.ux_t3lib_formmail.php";s:4:"8887";s:27:"class.ux_t3lib_htmlmail.php";s:4:"0ca6";s:21:"ext_conf_template.txt";s:4:"fe9e";s:12:"ext_icon.gif";s:4:"e3fe";s:17:"ext_localconf.php";s:4:"0729";s:14:"doc/manual.sxw";s:4:"7b4d";s:22:"lib/class.pearmail.php";s:4:"ca46";}',
	'suggests' => array(
	),
);

?>