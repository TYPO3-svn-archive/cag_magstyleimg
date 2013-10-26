<?php
$extensionPath = t3lib_extMgm::extPath('cag_magstyleimg');
return array(
	'tx_cagmagstyleimg_tvhook' => $extensionPath . 'Hooks/class.tx_cagmagstyleimg_tvhook.php',
	'tx_cagmagstyleimg_drawitem' => $extensionPath . 'Hooks/class.tx_cagmagstyleimg_drawitem.php',
	'tx_cagmagstyleimg_wizicon' => $extensionPath . 'Hooks/class.tx_cagmagstyleimg_wizicon.php'
);
?>