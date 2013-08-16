<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

// unserializing the configuration so we can use it here
$_EXTCONF = unserialize($_EXTCONF);

// load $TCA of tt_content for changes afterwards
t3lib_div::loadTCA('tt_content');

// insert new CE into tt_content $TCA
$TCA['tt_content']['columns']['CType']['config']['items'][] = Array(
	'0' => 'LLL:EXT:cag_magstyleimg/locallang_db.xml:tt_content.CType_pi1',
	'1' => $_EXTKEY.'_pi1'
);

// add the new flexform field to $TCA
$tempColumns = Array (
	'tx_cagmagstyleimg_flex' => Array(
		'exclude' => 1,
		'label' => 'LLL:EXT:cag_magstyleimg/locallang_db.xml:tt_content.tx_cagmagstyleimg_flex',
		'config' => Array (
			'type' => 'flex',
			'ds' => array(
				'default' => 'FILE:EXT:cag_magstyleimg/flexform_ds.xml',
			)
		)
	),
);

// use the $TCA definition from textpic
$TCA['tt_content']['types'][$_EXTKEY.'_pi1']['showitem'] = $TCA['tt_content']['types']['textpic']['showitem'];

t3lib_extMgm::addTCAcolumns('tt_content', $tempColumns, 1);
t3lib_extMgm::addToAllTCAtypes('tt_content', 'tx_cagmagstyleimg_flex', 'cag_magstyleimg_pi1', 'after:image');

// rename the pallette label for the imagewidth field
$TCA['tt_content']['types'][$_EXTKEY.'_pi1']['showitem'] = str_replace('LLL:EXT:cms/locallang_ttc.php:ALT.imgDimensions', 'LLL:EXT:cag_magstyleimg/locallang_db.xml:tt_content.imgDimensions', $TCA['tt_content']['types'][$_EXTKEY.'_pi1']['showitem']);

// check if dam_ttcontent is loaded
if (t3lib_extMgm::isLoaded('dam_ttcontent')) {	
	$dam_ttcontentConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['dam_ttcontent']);
	if ($dam_ttcontentConf['ctype_image_add_ref']) {
		if ($dam_ttcontentConf['ctype_image_add_orig_field']) {
			t3lib_extMgm::addToAllTCAtypes('tt_content','tx_damttcontent_files',$_EXTKEY.'_pi1','after:image');
		} else {
			$TCA['tt_content']['types'][$_EXTKEY.'_pi1']['showitem'] = str_replace(', image;', ', tx_damttcontent_files;', $TCA['tt_content']['types'][$_EXTKEY.'_pi1']['showitem']);
		}
	}
}

// exclude unnecessary formfields with subtype exclude;
$TCA['tt_content']['types'][$_EXTKEY.'_pi1']['subtype_value_field'] = 'CType';
$TCA['tt_content']['types'][$_EXTKEY.'_pi1']['subtypes_excludelist'] = Array(
	'cag_magstyleimg_pi1' => 'imageheight, imagecols, imageborder, image_noRows, image_effects',
);

// add a new content element icon
$TCA['tt_content']['ctrl']['typeicons']['cag_magstyleimg_pi1'] = t3lib_extMgm::extRelPath('cag_magstyleimg').'cag_magstyleimg_pi1.gif';

// add the CE wizard icon
if (TYPO3_MODE=='BE') $TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_cagmagstyleimg_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_cagmagstyleimg_pi1_wizicon.php';

// add TypoScript
t3lib_extMgm::addStaticFile($_EXTKEY,'static/','Magazine Style Images');

// XCLASSing Web>Page for image previews if activated
if ($_EXTCONF['activateThumbs'] == 1) {
	$TYPO3_CONF_VARS['BE']['XCLASS']['ext/cms/layout/class.tx_cms_layout.php'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.ux_tx_cms_layout.php';
}
?>
