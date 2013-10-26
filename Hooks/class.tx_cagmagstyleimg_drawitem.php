<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Torsten Schrade <schradt@uni-mainz.de>
 *  All rights reserved
 *
 *  This script is part of the Typo3 project. The Typo3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
/**
 *
 * @author		Torsten Schrade <schradt@uni-mainz.de>
 * @package		TYPO3
 * @subpackage	website
 */

class tx_cagmagstyleimg_drawitem implements \TYPO3\CMS\Backend\View\PageLayoutViewDrawItemHookInterface {

	/**
	 * Preprocesses the preview rendering of a content element.
	 *
	 * @param	\TYPO3\CMS\Backend\View\PageLayoutView $parentObject: Calling parent object
	 * @param	boolean			$drawItem:		Whether to draw the item using the default functionalities
	 * @param	string			$headerContent: Header content
	 * @param	string			$itemContent:	Item content
	 * @param	array			$row:			Record row of tt_content
	 * @return	void
	 */
	public function preProcess(\TYPO3\CMS\Backend\View\PageLayoutView &$parentObject, &$drawItem, &$outHeader, &$out, array &$row) {

		switch ($row['CType']) {

			case 'cag_magstyleimg_pi1':

				$drawItem = FALSE;

				if ($row['bodytext']) $out .= $parentObject->linkEditContent($parentObject->renderText($row['bodytext']), $row) . '<br />';

				if ($row['image']) $out .= $parentObject->thumbCode($row, 'tt_content', 'image') . '<br/>';

				break;
		}

	}

}
?>