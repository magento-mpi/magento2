/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

function topCart(elC) {
	var elC = $(elC);
	var elC=elC;
	var el=elC.parentNode;
	var check=0;
	var iTime = 2000;

	el.onmouseout = function() {
		tI=setTimeout("hideElement()",iTime);	
	};
	
	el.onmouseover = function() {
		if	(check==0)	{
			elC.zIndex=999;
			Effect.SlideDown(elC.id, { duration: 0.5 });			
			check=1;
			}
		if (tI) {clearTimeout(tI);}	
	};	

	hideElement = function() {
		Effect.SlideUp(elC.id, { duration: 0.5 });
		if (tI) {clearTimeout(tI);}
		elC.zIndex=1;	
		check=0;
	}	
}