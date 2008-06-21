<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');

//hook for templavoila
require_once (t3lib_extMgm::extPath('cag_magstyleimg').'pi1/class.tx_cagmagstyleimg_hooks.php');
$TYPO3_CONF_VARS['EXTCONF']['templavoila']['mod1']['renderPreviewContentClass'][] = 'tx_cagmagstyleimg_hooks';

?>
