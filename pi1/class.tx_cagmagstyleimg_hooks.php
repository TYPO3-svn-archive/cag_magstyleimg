<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Torsten Schrade <schradt@uni-mainz.de>
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
 * Class that adds a hook for display of preview images with Templavoila.
 *
 * @author Torsten Schrade <schradt@uni-mainz.de>
 * 
 *
 */


class tx_cagmagstyleimg_hooks {

	/*
	 *	This function uses a hook in the templavoila page module to
	 *	create thumbnails of the 'Magazine Image' Content Element
	 */

	function renderPreviewContent_preProcess($row,$table,&$alreadyRendered,$tvObj){
		global $LANG;		
		$out = '';

		switch($row['CType']){
			case 'cag_magstyleimg_pi1':
				if(method_exists($tvObj,'linkEdit')) {
					$out = $tvObj->linkEdit('<strong>'.$LANG->sL(t3lib_BEfunc::getItemLabel('tt_content','image'),1).'</strong><br /> ', $table, $row['uid']).t3lib_BEfunc::thumbCode ($row, $table, 'image', $tvObj->doc->backPath);
				} elseif(method_exists($tvObj,'link_edit')){
					$out = $tvObj->link_edit('<strong>'.$LANG->sL(t3lib_BEfunc::getItemLabel('tt_content','image'),1).'</strong><br /> ', $table, $row['uid']).t3lib_BEfunc::thumbCode ($row, $table, 'image', $tvObj->doc->backPath);
				}
				$alreadyRendered = true;
				break;
			default:
				break;			
		}		
		
		return $out;
		
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cag_magstyleimg/pi1/class.tx_cagmagstyleimg_hooks.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cag_magstyleimg/pi1/class.tx_cagmagstyleimg_hooks.php']);
}

?>
