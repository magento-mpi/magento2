/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/* SWIM2.0 :: Simple website menu
****************************************************************
* DOM scripting by brothercake -- http://www.brothercake.com/
* Licensed under GPL -- http://www.gnu.org/copyleft/gpl.html
****************************************************************
* For professional menu solutions visit -- http://www.udm4.com/ 
****************************************************************
*/



Event.observe(window, 'load', function() {
	//var verticals = new simpleMenu('menu-v', 'vertical');
	var horizontals = new simpleMenu('topnav', 'horizontal');
});



function simpleMenu(navid, orient){if(typeof document.getElementById == 'undefined' || /opera[\/ ][56]/i.test(navigator.userAgent)) { return; }this.iskde = navigator.vendor == 'KDE';this.isie = typeof document.all != 'undefined' && typeof window.opera == 'undefined' && !this.iskde;this.isoldsaf = navigator.vendor == 'Apple Computer, Inc.' && typeof XMLHttpRequest == 'undefined';this.tree = document.getElementById(navid);if(this.tree != null){this.items = this.tree.getElementsByTagName('li');this.itemsLen = this.items.length;var i = 0; do{this.init(this.items[i], this.isie, this.isoldsaf, this.iskde, navid, orient);}while (++i < this.itemsLen);}}simpleMenu.prototype.init = function(trigger, isie, isoldsaf, iskde, navid, ishoriz){trigger.menu = trigger.getElementsByTagName('ul').length > 0 ? trigger.getElementsByTagName('ul')[0] : null;trigger.link = trigger.getElementsByTagName('a')[0];trigger.issub = trigger.parentNode.id == navid;trigger.ishoriz = ishoriz == 'horizontal';this.openers = { 'm' : 'onmouseover', 'k' : (isie ? 'onactivate' : 'onfocus') };for(var i in this.openers){trigger[this.openers[i]] = function(e){if(!iskde) { trigger.link.className += (trigger.link.className == '' ? '' : ' ') + 'rollover'; }if(trigger.menu != null){if(trigger.ishoriz) { trigger.menu.style.left = (isie || isoldsaf) ? trigger.offsetLeft + 'px' : 'auto'; }trigger.menu.style.top = (trigger.ishoriz && trigger.issub) ? (isie || (trigger.ishoriz && isoldsaf)) ? trigger.link.offsetHeight + 'px' : 'auto' : (isie || (trigger.ishoriz && isoldsaf)) ? trigger.offsetTop + 'px' : '0';}};}this.closers = { 'm' : 'onmouseout', 'k' : (isie ? 'ondeactivate' : 'onblur') };for(i in this.closers){trigger[this.closers[i]] = function(e){this.related = (!e) ? window.event.toElement : e.relatedTarget;if(!this.contains(this.related)){if(!iskde) { trigger.link.className = trigger.link.className.replace(/[ ]?rollover/g, ''); }if(trigger.menu != null){trigger.menu.style[(trigger.ishoriz ? 'left' : 'top')] = trigger.ishoriz ? '-10000px' : '-100em';}}};}if(!isie){trigger.contains = function(node){if (node == null) { return false; }if (node == this) { return true; }else { return this.contains(node.parentNode); }};}}