<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');

// hook for templavoila
$TYPO3_CONF_VARS['EXTCONF']['templavoila']['mod1']['renderPreviewContentClass'][] = 'tx_cagmagstyleimg_tvhook';

?>
