<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');

$_EXTCONF = unserialize($_EXTCONF);    // unserializing the configuration so we can use it here:
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['smtpServer'] = $_EXTCONF['smtpServer'] ? $_EXTCONF['smtpServer'] : '';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['smtpUser'] = $_EXTCONF['smtpUser'] ? $_EXTCONF['smtpUser'] : '';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['smtpPassword'] = $_EXTCONF['smtpPassword'] ? $_EXTCONF['smtpPassword'] : '';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['smtpDebug'] = $_EXTCONF['smtpDebug'] ? $_EXTCONF['smtpDebug'] : '';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['smtpPort'] = $_EXTCONF['smtpPort'] ? $_EXTCONF['smtpPort'] : '';

$TYPO3_CONF_VARS['FE']['XCLASS']['t3lib/class.t3lib_htmlmail.php'] = t3lib_extMgm::extPath($_EXTKEY)."class.ux_t3lib_htmlmail.php";
$TYPO3_CONF_VARS['BE']['XCLASS']['t3lib/class.t3lib_htmlmail.php'] = t3lib_extMgm::extPath($_EXTKEY)."class.ux_t3lib_htmlmail.php";
$TYPO3_CONF_VARS['FE']['XCLASS']['t3lib/class.t3lib_formmail.php'] = t3lib_extMgm::extPath($_EXTKEY)."class.ux_t3lib_formmail.php";
$TYPO3_CONF_VARS['BE']['XCLASS']['t3lib/class.t3lib_formmail.php'] = t3lib_extMgm::extPath($_EXTKEY)."class.ux_t3lib_formmail.php";

?>
