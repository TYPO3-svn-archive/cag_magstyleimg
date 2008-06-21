<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2006 Harvey Kane (Original Script) <info@ragepank.com>
*  (c) 2006 Torsten Schrade (TYPO3 Implementation) <t.schrade@connecta.ag>
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
* For rendering of Content Element "Magazine Style Images".
* This is a TYPO3 implementation of the awesome PHP script for
*
* AUTOMATIC MAGAZINE LAYOUT by Harvey Kane (Version 0.9)
*
* Published on http://www.alistapart.com/articles/magazinelayout.
*
* Please have a look at his inspiring article for explanations on
* some of the methods of this class. You will find the original
* script in the /res folder of this extension.
*
* Thanks a lot Harvey for providing such great work!!!
*
* @author Torsten Schrade <t.schrade@connecta.ag>
*
*/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   71: class tx_cagmagstyleimg_pi1 extends tslib_pibase
 *   92:     function main ($content,$conf)
 *  185:     function _transpose($arr)
 *  201:     function addImage($filename,$url)
 *  240:     function insertImage($size,$file,$i)
 *  311:     function get1a($i1)
 *  331:     function get2a($i1,$i2)
 *  360:     function get3a($i1,$i2,$i3)
 *  401:     function get3b($i1,$i2,$i3)
 *  457:     function get4a($i1,$i2,$i3,$i4)
 *  503:     function get4b($i1,$i2,$i3,$i4)
 *  560:     function getHtml()
 *  775:     function useAsTextpic ($html)
 *
 * TOTAL FUNCTIONS: 12
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

require_once(PATH_tslib.'class.tslib_pibase.php');

class tx_cagmagstyleimg_pi1 extends tslib_pibase {

	var $prefixID = 'tx_cagmagstyleimg_pi1';
	var $scriptRelPath = 'pi1/class.tx_cagmagstyleimg_pi1.php';
	var $extKey = 'cag_magstyleimg';

	var $images = array();			// The images that shall be put into a magazine layout
	var $conf = array();			// The TypoScript config
	var $_numimages = 0;			// The number of images
	var $_fullwidth;				// The width of the magimage block
	var $_padding;					// The distance between the images
	var $_imgborderwidth;			// The border around each image
	var $_imgbordercolor;			// The color of the imageborder
	var $_imgborderstyle;			// The style attribute for the imageborder
	var $_imagecompression;			// The quality of the images
	var $_blockbgcolor;				// The background color of each magimage block;
	var $_blockborderwidth;			// The border around each magimage block
	var $_blockbordercolor;			// The color of the magimage border
	var $_blockborderstyle;			// The style attribute for the magimage border
	var $_captionArray = array();	// The array of imagecaptions
	var $_layoutWraps = array();	// Inner Wraps of the magazine image blocks

    function main($content,$conf) {

		// making TypoScript $conf generally available in class
		$this->conf = $conf;

		// check the TYPO3 version
		$version = t3lib_div::int_from_ver(TYPO3_version);

		// initialize the flexform
		$flexFieldName = 'tx_cagmagstyleimg_flex';
		$this->pi_initPIflexForm($flexFieldName);

		// get values from flexform: image sheet
		$this->imgPadding = $this->pi_getFFvalue($this->cObj->data[$flexFieldName], 'imgPadding');
		$this->imgBorderWidth = $this->pi_getFFvalue($this->cObj->data[$flexFieldName], 'imgBorderWidth');
		$this->imgBorderStyle = $this->pi_getFFvalue($this->cObj->data[$flexFieldName], 'imgBorderStyle');
		$this->imgBorderColor = htmlspecialchars($this->pi_getFFvalue($this->cObj->data[$flexFieldName], 'imgBorderColor'));

		// get values from flexform: block sheet
		$this->blockBGColor = htmlspecialchars($this->pi_getFFvalue($this->cObj->data[$flexFieldName], 'blockBGColor', 's_block'));
		$this->blockBorderWidth = $this->pi_getFFvalue($this->cObj->data[$flexFieldName], 'blockBorderWidth', 's_block');
		$this->blockBorderColor = htmlspecialchars($this->pi_getFFvalue($this->cObj->data[$flexFieldName], 'blockBorderColor', 's_block'));
		$this->blockBorderStyle = $this->pi_getFFvalue($this->cObj->data[$flexFieldName], 'blockBorderStyle', 's_block');


		// BLOCK SETUP

    	// define the width for the output area (pixels)
		$this->_fullwidth = $this->cObj->cObjGetSingle($conf['blockConfig.']['width'], $conf['blockConfig.']['width.']);
		$GLOBALS['TSFE']->register['msiBlockWidth'] = $this->_fullwidth;

		// define if a background color for the block is set from flexform or from TS and write it to a global register for later substitution
		if ($this->blockBGColor != '') {
			$GLOBALS['TSFE']->register['msiBlockBgColor'] = $this->blockBGColor;
		} else {
			$GLOBALS['TSFE']->register['msiBlockBgColor'] = $conf['blockConfig.']['bgColor'];
		}

		// define border values around the block from flexform, otherwise use TS values
		($this->blockBorderStyle != '') ? $this->_blockborderstyle = $this->blockBorderStyle : $this->_blockborderstyle = $conf['blockConfig.']['border.']['style'];
		($this->blockBorderColor != '') ? $this->_blockbordercolor = $this->blockBorderColor : $this->_blockbordercolor = $conf['blockConfig.']['border.']['color'];
		($this->blockBorderWidth != '') ? $this->_blockborderwidth = $this->blockBorderWidth : $this->_blockborderwidth = $conf['blockConfig.']['border.']['width'];
		$GLOBALS['TSFE']->register['msiBlockBorder'] = $this->_blockborderwidth.'px '.$this->_blockborderstyle.' '.$this->_blockbordercolor;

		// define text margins in relation the block for usage as textpic
		if ($conf['textMargin.']['addBorderWidth']) {
			($this->_blockborderwidth != '') ? $textMargin = $conf['textMargin'] + ($this->_blockborderwidth*2) : $textMargin = $conf['textMargin'];
		} else {
			$textMargin = $conf['textMargin'];
		}
		// add the text margin to the global textpic registers
		if($version > 4000000) {
			$GLOBALS['TSFE']->register['rowWidthPlusTextMargin'] = $this->_fullwidth + $textMargin;
		} else {
			$GLOBALS['TSFE']->register['rowwidth'] = $this->_fullwidth + $textMargin;
		}

		// LAYOUT WRAPS
		$this->_layoutWraps = $conf['layoutWraps.'];

		// IMAGE SETUP

		// Get the images
		$imgList = trim($this->cObj->stdWrap($conf['imgList'], $conf['imgList.']));
		if (!$imgList)	{
			if (is_array($conf['stdWrap.']))	{
				return $this->cObj->stdWrap($content, $conf['stdWrap.']);
			}
			return $content;
		}
		$images = t3lib_div::trimExplode(',', $imgList);

		// Get the path to the images
		$imgPath = $this->cObj->stdWrap($conf['imgPath'], $conf['imgPath.']);

		// Define padding around each image
		($this->imgPadding != '') ? $this->_padding = $this->imgPadding : $this->_padding = $conf['imgConfig.']['imgPadding'];

		// Define border around each image if set in flexform, otherwise use TS values
		($this->imgBorderWidth != '') ? $this->_imgborderwidth = $this->imgBorderWidth : $this->_imgborderwidth = $conf['imgConfig.']['border.']['width'];
		($this->imgBorderStyle != '') ? $this->_imgborderstyle = $this->imgBorderStyle : $this->_imgborderstyle = $conf['imgConfig.']['border.']['style'];
		($this->imgBorderColor != '') ? $this->_imgbordercolor = $this->imgBorderColor : $this->_imgbordercolor = $conf['imgConfig.']['border.']['color'];

		// Image compression
		$this->_imagecompression = $this->cObj->cObjGetSingle($conf['imgConfig.']['image_compression'], $conf['imgConfig.']['image_compression.']);

		// captions
		if ($this->cObj->data['imagecaption'] != '') {$this->_captionArray = t3lib_div::trimExplode(chr(10), $this->cObj->data['imagecaption']);}

		// hand the imgages over for analysis
		$a = 0;
		if (!empty($images)) {

			foreach ($images as $key => $value) {

				/* At the moment only 8 images are supported;
				 * The break has to be done here since we can't limit image supply in $TCA
				 * for the standard image field - it's used by other CTypes as well ;) */

				if ($a > 7) break;

				$this->addImage($images[$key], $imgPath);

				$a++;
			}
		}

		// accumulate the HTML for the imageblocks
		$content .= $this->getHtml();

    	return $content;

    }


	/**
	 * Converts the format of a 2D array from $arr[a][b] to $arr[b][a] - used for sorting the array
	 *
	 * @param	[type]		$filename: ...
	 * @param	[type]		$url: ...
	 * @return	[type]		...
	 */
	function _transpose($arr) {
		foreach($arr as $keyx => $valx) {
			foreach($valx as $keyy => $valy) {
				$newarr[$keyy][$keyx] = $valy;
			}
		}
		return $newarr;
	}


	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$filename: ...
	 * @param	[type]		$url: ...
	 * @return	[type]		...
	 */
	function addImage($filename,$imgPath) {

		/* get the image dimensions doing it the TYPO3 way */
		$gifbuilder = t3lib_div::makeInstance('tslib_gifbuilder');
		$imageInfo = $gifbuilder->getImageDimensions($imgPath.$filename);

		$w = $imageInfo[0];
		$h = $imageInfo[1];

		/* don't include zero sized images */
		if (($h == 0) || ($w == 0)) return false;

		/* Find the ration of width:height */
		$ratio = $w / $h;

		/* Set format based on the dimensions */
		$format = ($w > $h) ? 'landscape' : 'portrait';

		/* Keep a count on the total number of images */
		$this->_numimages++;

		/* Save all image details to an array */
		$i = $this->_numimages - 1;

		$GLOBALS['TSFE']->register['IMAGE_NUM'] = $i;
		$GLOBALS['TSFE']->register['IMAGE_NUM_CURRENT'] = $i;

		$this->images[$i] = array();
		$this->images[$i]['number'] = $i;
		$this->images[$i]['filename'] = $filename;
		$this->images[$i]['imgPath'] = $imgPath;
		$this->images[$i]['fullImgPath'] = $imageInfo[3];
		$this->images[$i]['ext'] = $imageInfo[2];
		$this->images[$i]['format'] = $format;
		$this->images[$i]['ratio'] = $ratio;
		$this->images[$i]['w'] = $w;
		$this->images[$i]['h'] = $h;
		$this->images[$i]['altText'] = $this->cObj->cObjGetSingle($this->conf['imgConfig.']['altText'], $this->conf['imgConfig.']['altText.']);
		$this->images[$i]['titleText'] = $this->cObj->cObjGetSingle($this->conf['imgConfig.']['titleText'], $this->conf['imgConfig.']['titleText.']);
		$this->images[$i]['longdescURL'] = $this->cObj->cObjGetSingle($this->conf['imgConfig.']['longdescURL'], $this->conf['imgConfig.']['longdescURL.']);
		$this->images[$i]['caption'] = $this->_captionArray[$i];

		return true;
	}


	/* This returns the img tag with the scaled image */

	function insertImage($size,$file,$i) {

		// this is used for the caption/alt/titleText split
		$GLOBALS['TSFE']->register['IMAGE_NUM'] = $i;
		$GLOBALS['TSFE']->register['IMAGE_NUM_CURRENT'] = $i;

		// begin to set $conf array for usage with IMAGE obj
		unset($this->conf['imgConfig.']['file.']['width']);
		unset($this->conf['imgConfig.']['file.']['height']);
		unset($this->conf['imgConfig.']['altText.']);
		unset($this->conf['imgConfig.']['titleText.']);
		unset($this->conf['imgConfig.']['longescURL.']);

		// inserting the file
		$this->conf['imgConfig.']['file'] = $file;

		// set inline styles for the imageborders
		if ($this->_imgborderwidth != '') {$borderStyle = 'border: '.$this->_imgborderwidth.'px '.$this->_imgborderstyle.' '.$this->_imgbordercolor.'; ';}

		// the params should be accumulated in a later version so that other values could be set from TS as well
		$this->conf['imgConfig.']['params'] .= 'style="margin: '.$this->_padding.'px; '.$borderStyle.'padding: 0px;"';

		// decide between fixedwidth or fixedheight for scaling (was done in image.php and is now handed over to IMAGE cObj in TYPO3)
		if (substr($size, 0, 1) == 'h') {

			$this->conf['imgConfig.']['file.']['width'] = floor(str_replace('h','',$size) * $this->images[$i]['ratio']);
			$this->conf['imgConfig.']['file.']['height'] = str_replace('h','',$size);

		} elseif (substr($size, 0, 1) == 'w') {

			$this->conf['imgConfig.']['file.']['width'] = str_replace('w','',$size);
			$this->conf['imgConfig.']['file.']['height'] = floor(str_replace('w','',$size) / $this->images[$i]['ratio']);

		} elseif ($this->images[$i]['width'] > $this->images[$i]['height']) {

			$this->conf['imgConfig.']['file.']['width'] = floor(str_replace('h','',$size) * $this->images[$i]['ratio']);
			$this->conf['imgConfig.']['file.']['height'] = str_replace('h','',$size);

		} else {

			$this->conf['imgConfig.']['file.']['width'] = str_replace('w','',$size);
			$this->conf['imgConfig.']['file.']['height'] = floor(str_replace('w','',$size) / $this->images[$i]['ratio']);

		}

		// set image compression
		if (isset($this->_imagecompression)) {

			$this->conf['imgConfig.']['file.']['params'].= ' '.$this->cObj->image_compression[$this->_imagecompression]['params'];
			$this->conf['imgConfig.']['file.']['ext'] = $this->cObj->image_compression[$this->_imagecompression]['ext'];

			unset($this->conf['imgConfig.']['file.']['ext.']);
		}

		// set alt/title/longesc attributes (overwrite them due to possible swap of image order)
		$this->conf['imgConfig.']['altText'] = $this->images[$i]['altText'];
		$this->conf['imgConfig.']['titleText'] = $this->images[$i]['titleText'];
		$this->conf['imgConfig.']['longescURL'] = $this->images[$i]['longdescURL'];

		// write the current image information into a global register so that it can be fetched with TS; this is necessary to provide
		// for all situations in which the image order is not the same as in the DB field
		$GLOBALS['TSFE']->register['MAG_IMG_CURRENT'] = $this->images[$i];

		// handing over to tslib_cObj for doing the resizing stuff
		$imgTag = $this->cObj->IMAGE($this->conf['imgConfig.']);

		// unset the params for the next image
		unset ($this->conf['imgConfig.']['params']);

		return $imgTag;
	}


	/*
	IMAGE LAYOUTS
	=============
	These layouts are coded based on the number of images.
	Some fairly heavy mathematics is used to calculate the image sizes and the excellent calculators at
	http://www.quickmath.com/ were very useful. Each of these layouts outputs a small piece of HTML code with the images.
	*/

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$i1: ...
	 * @return	[type]		...
	 */
	function get1a($i1) {
		/*
		111 or 1
		       1
		*/

		$s = floor($this->_fullwidth - ($this->_padding * 2 + $this->_imgborderwidth));

		$html = '';
		$html .= $this->cObj->stdWrap($this->insertImage(''.$s,$this->images[$i1]['fullImgPath'],$i1), $this->_layoutWraps['1a.']['1.']);

		return $html;

	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$i1: ...
	 * @param	[type]		$i2: ...
	 * @return	[type]		...
	 */
	function get2a($i1,$i2) {
		/*
		1122
		Equation: t = 4p + ha + hb Variable: h
		*/

		$a = $this->images[$i1]['ratio'];
		$b = $this->images[$i2]['ratio'];
		$t = $this->_fullwidth;
		$p = $this->_padding + $this->_imgborderwidth;

		$h1 = floor( (4*$p - $t) / (-$a - $b) );

		$html = '';
		$html .= $this->cObj->stdWrap($this->insertImage('h'.$h1,$this->images[$i1]['fullImgPath'],$i1), $this->_layoutWraps['2a.']['1.']);
		$html .= $this->cObj->stdWrap($this->insertImage('h'.$h1,$this->images[$i2]['fullImgPath'],$i2), $this->_layoutWraps['2a.']['2.']);

		return $html;
	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$i1: ...
	 * @param	[type]		$i2: ...
	 * @param	[type]		$i3: ...
	 * @return	[type]		...
	 */
	function get3a($i1,$i2,$i3) {
		/*
		1223
		*/

		/* To save space in the equation */
		$a = $this->images[$i3]['ratio'];
		$b = $this->images[$i1]['ratio'];
		$c = $this->images[$i2]['ratio'];
		$t = $this->_fullwidth;
		$p = $this->_padding + $this->_imgborderwidth;

		/*
		Enter the following data at http://www.hostsrv.com/webmab/app1/MSP/quickmath/02/pageGenerate?site=quickmath&s1=equations&s2=solve&s3=advanced#reply
		EQUATIONS
		t = 6p + ah + bh + ch
		VARIABLES
		h
		*/

		$h1 = floor(
		(6 * $p - $t)
		/
		(-$a -$b -$c)
		);

		$html = '';
		$html .= $this->cObj->stdWrap($this->insertImage('h'.$h1,$this->images[$i1]['fullImgPath'],$i1), $this->_layoutWraps['3a.']['1.']);
		$html .= $this->cObj->stdWrap($this->insertImage('h'.$h1,$this->images[$i3]['fullImgPath'],$i3), $this->_layoutWraps['3a.']['2.']);
		$html .= $this->cObj->stdWrap($this->insertImage('h'.$h1,$this->images[$i2]['fullImgPath'],$i2), $this->_layoutWraps['3a.']['3.']);

		return $html;
	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$i1: ...
	 * @param	[type]		$i2: ...
	 * @param	[type]		$i3: ...
	 * @return	[type]		...
	 */
	function get3b($i1,$i2,$i3) {
		/*
		1133
		2233
		*/

		/* To save space in the equation */
		$a = $this->images[$i3]['ratio'];
		$b = $this->images[$i1]['ratio'];
		$c = $this->images[$i2]['ratio'];
		$t = $this->_fullwidth;
		$p = $this->_padding + $this->_imgborderwidth;

		/*
		Enter the following data at http://www.hostsrv.com/webmab/app1/MSP/quickmath/02/pageGenerate?site=quickmath&s1=equations&s2=solve&s3=advanced#reply
		EQUATIONS
		x/a = w/b + w/c + 2p
		w+x+4p = t
		VARIABLES
		w
		x
		*/

		/* column with 2 small images */
		$w1 = floor(
		-(
		(2 * $a * $b * $c * $p + 4 * $b * $c * $p - $b * $c * $t)
		/
		($a * $b + $c * $b + $a * $c)
		)
		);

		/* column with 1 large image */
		$w2 = floor(
		($a * (-4 * $b * $p + 2 * $b * $c * $p - 4 * $c * $p + $b * $t + $c * $t))
		/
		($a * $b + $c * $b + $a * $c)
		);

		$html = '';
		$html .= $this->cObj->stdWrap($this->insertImage('w'.$w2,$this->images[$i3]['fullImgPath'],$i3), $this->_layoutWraps['3b.']['1.']);
		$html .= $this->cObj->stdWrap($this->insertImage('w'.$w1,$this->images[$i1]['fullImgPath'],$i1), $this->_layoutWraps['3b.']['2.']);
		$html .= $this->cObj->stdWrap($this->insertImage('w'.$w1,$this->images[$i2]['fullImgPath'],$i2), $this->_layoutWraps['3b.']['3.']);

		return $html;
	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$i1: ...
	 * @param	[type]		$i2: ...
	 * @param	[type]		$i3: ...
	 * @param	[type]		$i4: ...
	 * @return	[type]		...
	 */
	function get4a($i1,$i2,$i3,$i4) {
		/*
		1234
		*/

		/* To save space in the equation */
		$a = $this->images[$i1]['ratio'];
		$b = $this->images[$i2]['ratio'];
		$c = $this->images[$i3]['ratio'];
		$d = $this->images[$i4]['ratio'];
		$t = $this->_fullwidth;
		$p = $this->_padding + $this->_imgborderwidth;

		/*
		Enter the following data at http://www.hostsrv.com/webmab/app1/MSP/quickmath/02/pageGenerate?site=quickmath&s1=equations&s2=solve&s3=advanced#reply
		EQUATIONS
		t = 6p + ah + bh + ch + dh
		VARIABLES
		h
		*/

		$h1 = floor(
		(8 * $p - $t)
		/
		(-$a -$b -$c -$d)
		);

		$html = '';
		$html .= $this->cObj->stdWrap($this->insertImage('h'.$h1,$this->images[$i1]['fullImgPath'],$i1), $this->_layoutWraps['4a.']['1.']);
		$html .= $this->cObj->stdWrap($this->insertImage('h'.$h1,$this->images[$i2]['fullImgPath'],$i2), $this->_layoutWraps['4a.']['2.']);
		$html .= $this->cObj->stdWrap($this->insertImage('h'.$h1,$this->images[$i3]['fullImgPath'],$i3), $this->_layoutWraps['4a.']['3.']);
		$html .= $this->cObj->stdWrap($this->insertImage('h'.$h1,$this->images[$i4]['fullImgPath'],$i4), $this->_layoutWraps['4a.']['4.']);

		return $html;
	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$i1: ...
	 * @param	[type]		$i2: ...
	 * @param	[type]		$i3: ...
	 * @param	[type]		$i4: ...
	 * @return	[type]		...
	 */
	function get4b($i1,$i2,$i3,$i4) {
		/*
		11444
		22444
		33444
		*/

		/* To save space in the equation */
		$a = $this->images[$i4]['ratio'];
		$b = $this->images[$i1]['ratio'];
		$c = $this->images[$i2]['ratio'];
		$d = $this->images[$i3]['ratio'];
		$t = $this->_fullwidth;
		$p = $this->_padding + $this->_imgborderwidth;

		/*
		Enter the following data at http://www.hostsrv.com/webmab/app1/MSP/quickmath/02/pageGenerate?site=quickmath&s1=equations&s2=solve&s3=advanced#reply
		EQUATIONS
		x/a = w/b + w/c + 2p
		w+x+4p = t
		VARIABLES
		w
		x
		*/

		/* column with 3 small images */
		$w1 = floor(
		-(
		(4 * $a * $b * $c * $d * $p + 4 * $b * $c * $d * $p - $b * $c * $d * $t)
		/
		($a * $b * $c + $a * $d * $c + $b * $d * $c + $a * $b * $d)
		)
		);

		/* column with 1 large image */
		$w2 = floor(
		-(
		(-4 * $p - (-(1/$c) -(1/$d) -(1/$b)) * (4 * $p - $t) )
		/
		( (1/$b) + (1/$c) + (1/$d) + (1/$a) )
		)
		);

		$html = '';
		$html .= $this->cObj->stdWrap($this->insertImage('w'.$w2,$this->images[$i4]['fullImgPath'],$i4), $this->_layoutWraps['4b.']['1.']);
		$html .= $this->cObj->stdWrap($this->insertImage('w'.$w1,$this->images[$i1]['fullImgPath'],$i1), $this->_layoutWraps['4b.']['2.']);
		$html .= $this->cObj->stdWrap($this->insertImage('w'.$w1,$this->images[$i2]['fullImgPath'],$i2), $this->_layoutWraps['4b.']['3.']);
		$html .= $this->cObj->stdWrap($this->insertImage('w'.$w1,$this->images[$i3]['fullImgPath'],$i3), $this->_layoutWraps['4b.']['4.']);

		return $html;
	}

	/**
	 * This function puts together the full HTML for the magazine imageblocks by first sorting the image arrays and then calling
	 * the appropriate layout function.
	 *
	 * @return	string		The complete HTML for the magazine imageblock
	 */
	function getHtml() {

		// Open the magimg DIV
		$html = '';

		$blockWrap = explode('|', $this->conf['blockConfig.']['blockWrap']);
		$blockWrap[1] = $this->cObj->insertData($blockWrap[1]);

		// start the magazine imageblock
		$html .= $this->cObj->insertData($blockWrap[0]);

		// start the image sorting / processing
		if (!empty($this->images)) {

			// sort the images array landscape first, then portrait

			#t3lib_div::debug($this->images, 'before:');

			$this->images = $this->_transpose($this->images);

			#t3lib_div::debug($this->images, 'transpose 1:');

			array_multisort($this->images['format'], SORT_STRING, SORT_ASC, $this->images['filename'], $this->images['fullImgPath'], $this->images['ext'], $this->images['ratio'], $this->images['w'], $this->images['h'], $this->images['altText'], $this->images['titleText'], $this->images['longdescURL'], $this->images['caption']);

			#t3lib_div::debug($this->images, 'array_multisort:');

			$this->images = $this->_transpose($this->images);

			#t3lib_div::debug($this->images, 'transpose 2:');

			/* Profile explains the makeup of the images (landscape vs portrait) so we can use the best layout eg. LPPP or LLLP */
			$profile = '';
			foreach ($this->images as $i) {
				$profile .= $i['format'] == 'landscape' ? 'L' : 'P';
			}

		// if there are no images, return
		} else {

			$html .= $blockWrap[1];

			if ($this->conf['textConfig.'] || $this->conf['layout.']) {$html = $this->useAsTextpic($html);}

			return $html;
		}

		//t3lib_div::debug($profile);

		/* 1 Images */
		if ($this->_numimages == 1) {
			$html .= $this->get1a(0);
		}

		/* 2 Images */
		if ($this->_numimages == 2) {
			$this->rearrangeCaptions('0,1');
			$html .= $this->get2a(0,1);
		}

		/* 3 Images */
		if ($this->_numimages == 3) {
			if ($profile == 'LLL') {
				$this->rearrangeCaptions('2,0,1');
				$html .= $this->get3b(0,1,2);
				//$html .= $this->get2a(1,2);
				//$html .= $this->get1a(0);
			} else {
				$this->rearrangeCaptions('2,0,1');
				$html .= $this->get3b(0,1,2);
			}
		}

		/* 4 Images */
		if ($this->_numimages == 4) {

			if ($profile == 'LLLP') {
				$this->rearrangeCaptions('3,0,1,2');
				$html .= $this->get4b(0,1,2,3);
			} elseif ($profile == 'LPPP') {
				$this->rearrangeCaptions('1,3,2,0');
				$html .= $this->get3a(1,2,3);
				$html .= $this->get1a(0);
			} else { // LLLL LLPP PPPP
				$this->rearrangeCaptions('2,0,1,3');
				$html .= $this->get2a(2,0);
				$html .= $this->get2a(1,3);
			}
		}

		/* 5 Images */
		if ($this->_numimages == 5) {
			if ($profile == 'LLLLL') {
				$this->rearrangeCaptions('0,1,2,3,4');
				$html .= $this->get3a(0,1,2);
				$html .= $this->get2a(3,4);
			} elseif ($profile == 'LLLLP') {
				$this->rearrangeCaptions('4,0,1,2,3');
				$html .= $this->get3b(0,1,4);
				$html .= $this->get2a(2,3);
			} elseif ($profile == 'LLLPP') {
				$this->rearrangeCaptions('4,0,1,2,3');
				$html .= $this->get3b(0,1,4);
				$html .= $this->get2a(2,3);
			} elseif ($profile == 'LLPPP') {
				$this->rearrangeCaptions('4,2,3,0,1');
				$html .= $this->get3b(2,3,4);
				$html .= $this->get2a(0,1);
			} elseif ($profile == 'LPPPP') {
				$this->rearrangeCaptions('4,2,3,0,1');
				$html .= $this->get3b(2,3,4);
				$html .= $this->get2a(0,1);
			} elseif ($profile == 'PPPPP') {
				$this->rearrangeCaptions('4,0,1,3,2');
				$html .= $this->get2a(4,0);
				$html .= $this->get3a(1,2,3);
			}
		}

		/* 6 Images */
		if ($this->_numimages == 6) {
			if ($profile == 'LLLLLL') {
				$this->rearrangeCaptions('0,1,2,3,4,5');
				$html .= $this->get2a(0,1);
				$html .= $this->get2a(2,3);
				$html .= $this->get2a(4,5);
			} elseif ($profile == 'LLLLLP') {
				$this->rearrangeCaptions('5,0,1,2,3,4');
				$html .= $this->get4b(0,1,2,5);
				$html .= $this->get2a(3,4);
			} elseif ($profile == 'LLLLPP') {
				$this->rearrangeCaptions('4,0,1,5,2,3');
				$html .= $this->get3b(0,1,4);
				$html .= $this->get3b(2,3,5);
			} elseif ($profile == 'LLLPPP') {
				$this->rearrangeCaptions('5,0,1,4,2,3');
				$html .= $this->get3b(0,1,5);
				$html .= $this->get3b(2,3,4);
			} elseif ($profile == 'LLPPPP') {
				$this->rearrangeCaptions('4,0,2,5,1,3');
				$html .= $this->get3b(0,2,4);
				$html .= $this->get3b(1,3,5);
			} elseif ($profile == 'LPPPPP') {
				$this->rearrangeCaptions('5,0,1,2,4,3');
				$html .= $this->get3b(0,1,5);
				$html .= $this->get3a(2,3,4);
			} elseif ($profile == 'PPPPPP') {
				$this->rearrangeCaptions('3,5,4,0,2,1');
				$html .= $this->get3a(3,4,5);
				$html .= $this->get3a(0,1,2);
			}
		}

		/* 7 Images */
		if ($this->_numimages == 7) {
			if ($profile == 'LLLLLLL') {
				$this->rearrangeCaptions('0,2,1,3,4,5,6');
				$html .= $this->get3a(0,1,2);
				$html .= $this->get2a(3,4);
				$html .= $this->get2a(5,6);
			} elseif ($profile == 'LLLLLLP') {
				$this->rearrangeCaptions('6,0,1,2,3,5,4');
				$html .= $this->get4b(0,1,2,6);
				$html .= $this->get3a(3,4,5);
			} elseif ($profile == 'LLLLLPP') {
				$this->rearrangeCaptions('5,0,1,2,6,3,4');
				$html .= $this->get4b(0,1,2,5);
				$html .= $this->get3b(3,4,6);
			} elseif ($profile == 'LLLLPPP') {
				$this->rearrangeCaptions('5,0,1,6,2,3,4');
				$html .= $this->get3b(0,1,5);
				$html .= $this->get4b(2,3,4,6);
			} elseif ($profile == 'LLLPPPP') {
				$this->rearrangeCaptions('5,0,1,6,2,3,4');
				$html .= $this->get3b(0,1,5);
				$html .= $this->get4b(2,3,4,6);
			} elseif ($profile == 'LLPPPPP') {
				$this->rearrangeCaptions('4,6,5,0,1,2,3');
				$html .= $this->get3a(4,5,6);
				$html .= $this->get2a(0,1);
				$html .= $this->get2a(2,3);
			} elseif ($profile == 'LPPPPPP') {
				$this->rearrangeCaptions('0,2,1,6,3,4,5');
				$html .= $this->get3a(0,1,2);
				$html .= $this->get4b(3,4,5,6);
			} elseif ($profile == 'PPPPPPP') {
				$this->rearrangeCaptions('0,1,2,3,6,4,5');
				$html .= $this->get4a(0,1,2,3);
				$html .= $this->get3b(4,5,6);
			}
		}

		/* 8 Images */
		if ($this->_numimages >= 8) {
			/*
			Note this code is applied for 8 or more images - any images over 8 are ignored. Adding support
			for more than 8 images would be easy, but the layouts do start losing their effect as more images
			are added.
			*/
			if ($profile == 'LLLLLLLL') {
				$this->rearrangeCaptions('0,2,1,3,4,5,7,6');
				$html .= $this->get3a(0,1,2);
				$html .= $this->get2a(3,4);
				$html .= $this->get3a(5,6,7);
			} elseif ($profile == 'LLLLLLLP') {
				$this->rearrangeCaptions('7,0,2,1,3,4,5,6');
				$html .= $this->get4b(0,1,2,7);
				$html .= $this->get2a(3,4);
				$html .= $this->get2a(5,6);
			} elseif ($profile == 'LLLLLLPP') {
				$this->rearrangeCaptions('6,0,1,2,7,3,4,5');
				$html .= $this->get4b(0,1,2,6);
				$html .= $this->get4b(3,4,5,7);
			} elseif ($profile == 'LLLLLPPP') {
				$this->rearrangeCaptions('6,0,1,2,7,3,4,5');
				$html .= $this->get4b(0,1,2,6);
				$html .= $this->get4b(3,4,5,7);
			} elseif ($profile == 'LLLLPPPP') {
				$this->rearrangeCaptions('6,0,1,2,7,3,4,5');
				$html .= $this->get4b(0,1,2,6);
				$html .= $this->get4b(3,4,5,7);
			} elseif ($profile == 'LLLPPPPP') {
				$this->rearrangeCaptions('4,6,5,0,1,2,7,3');
				$html .= $this->get3a(4,5,6);
				$html .= $this->get2a(0,1);
				$html .= $this->get3a(2,3,7);
			} elseif ($profile == 'LLPPPPPP') {
				$this->rearrangeCaptions('7,5,6,0,1,4,2,3');
				$html .= $this->get3b(5,6,7);
				$html .= $this->get2a(0,1);
				$html .= $this->get3b(2,3,4);
			} elseif ($profile == 'LPPPPPPP') {
				$this->rearrangeCaptions('7,5,6,0,1,4,2,3');
				$html .= $this->get3b(5,6,7);
				$html .= $this->get2a(0,1);
				$html .= $this->get3b(2,3,4);
			} elseif ($profile == 'PPPPPPP') {
				$this->rearrangeCaptions('3,0,1,2,7,4,5,6');
				$html .= $this->get4a(0,1,2,3);
				$html .= $this->get4a(4,5,6,7);
			} else {
				$this->rearrangeCaptions('7,5,4,1,0,6,2,3');
				$html .= $this->get3b(5,4,7);
				$html .= $this->get2a(1,0);
				$html .= $this->get3b(2,3,6);
			}
		}

		/* Close the magimage DIV */
		$html .= $blockWrap[1];

		/* If the plugin is used as text/w image element get wraps and text*/
		if ($this->conf['textConfig.'] || $this->conf['layout.']) {$html = $this->useAsTextpic($html);}

		// Caption is inserted
		$caption = $this->cObj->cObjGetSingle($this->conf['imgConfig.']['caption'], $this->conf['imgConfig.']['caption.']);
		$html = str_replace('###CAPTION###', $caption, $html);

		return $html;
	}


	/**
	 * Rearranges the image captions to the order of images in the magazine blocks. Writes changes into $this->cObj->data['imagecaption'].
	 * This function is necessary because the images/captions order in BE can be different from FE. By executing this function the BE order
	 * doesn't matter anymore.
	 *
	 * @param	string		The sequence of captions/images for the magazine layout
	 * @return				void
	 */
	function rearrangeCaptions ($sequence) {

		$newCaptionOrder = t3lib_div::trimExplode(',',$sequence);

		#t3lib_div::debug($newCaptionOrder, '$newCaptionOrder before:');

		foreach ($newCaptionOrder as $key => $value) {

			#t3lib_div::debug($this->images[$value]['caption']);

			if ($this->images[$value]['caption'] != '') {
				$newCaptionOrder[$key] = $this->images[$value]['caption'];
			} else {
				unset ($newCaptionOrder[$key]);
			}
		}

		#t3lib_div::debug($newCaptionOrder, '$newCaptionOrder after:');

		if (!empty($newCaptionOrder)) {$this->cObj->data['imagecaption'] = implode(chr(10),$newCaptionOrder);}

		#t3lib_div::debug($this->cObj->data['imagecaption'], 'After:');

		return;
	}


	/**
	 * Includes the magazine imageblock into the outer wraps set in tt_content.image for positioning of block and text with CSS.
	 *
	 * @param	string		The magazine imageblock HTML
	 * @return	string		The accumulated content with bodytext and wraps from tt_content.image
	 */
	function useAsTextpic($html) {

		// have we got text?
		if (is_array($this->conf['text.']))	{
				$content .= $this->cObj->stdWrap($this->cObj->cObjGet($this->conf['text.'], 'text.'), $this->conf['text.']);
		}

		// put the magimageblock into the standard textpic wrap
		$output = $this->cObj->cObjGetSingle($this->conf['layout'], $this->conf['layout.']);
		$output = str_replace('###TEXT###', $content, $output);
		$output = str_replace('###IMAGES###', $html, $output);
		// not used and therefore emptied
		$output = str_replace('###CLASSES###', '', $output);

		return $output;

	}

	function getLayoutWrap ($layout) {

		$this->cObj->stdWrap($this->cObj->cObjGet($this->conf['text.'], 'text.'), $this->conf['text.']);

	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cag_magstyleimg/pi1/class.tx_cagmagstyleimg_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cag_magstyleimg/pi1/class.tx_cagmagstyleimg_pi1.php']);
}
?>
