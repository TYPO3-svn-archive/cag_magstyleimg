<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

// unserializing the configuration so we can use it here
$_EXTCONF = unserialize($_EXTCONF);

// load $TCA of tt_content for changes afterwards
t3lib_div::loadTCA('tt_content');

// insert new CE into tt_content $TCA
$TCA['tt_content']['columns']['CType']['config']['items'][] = Array(
	'0' => 'LLL:EXT:cag_magstyleimg/locallang_db.xml:tt_content.CType_pi1',
	'1' => $_EXTKEY.'_pi1',
	'2' => 'EXT:cag_magstyleimg/res/cag_magstyleimg_pi1.gif'
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

// exclude unnecessary formfields with subtype exclude;
$TCA['tt_content']['types'][$_EXTKEY.'_pi1']['subtype_value_field'] = 'CType';
$TCA['tt_content']['types'][$_EXTKEY.'_pi1']['subtypes_excludelist'] = Array(
	'cag_magstyleimg_pi1' => 'imageheight, imagecols, imageborder, image_noRows, image_effects',
);

// add the CE wizard icon
if (TYPO3_MODE=='BE') $TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_cagmagstyleimg_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'Hooks/class.tx_cagmagstyleimg_wizicon.php';

// add TypoScript
t3lib_extMgm::addStaticFile($_EXTKEY,'static/','Magazine Style Images');

// image previews in BE
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem'][] = 'EXT:cag_magstyleimg/Hooks/class.tx_cagmagstyleimg_drawitem.php:tx_cagmagstyleimg_drawitem';
?>