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
	var el=elC.up(0);
	var check=0;
	var iTime = 2000;
    var tIinterval;   
    
	el.onmouseout = function() {
		tIinterval=setTimeout("hideElement()",iTime);	
	};
	
	el.onmouseover = function() {
		if	(check==0)	{
			elC.zIndex=999;
			new Effect.SlideDown(elC.id, { duration: 0.5 });			
			check=1;
			}
		if (tIinterval) {clearTimeout(tIinterval);}	
	};	

	hideElement = function() {
		new Effect.SlideUp(elC.id, { duration: 0.5 });
		if (tIinterval) {clearTimeout(tIinterval);}
		elC.zIndex=1;	
		check=0;
	}	
}

function initBundle(pName) {
    productName = pName;
    bundleOptions = $('options-container');
    bundleOptions.hide();
    bundleOptions.addClassName('bundleProduct');
    bCheck = 1;
}
function openCustomize() {
    if (bCheck == 1) {
        $('messages_product_view').insert ({'before':'<div id="customizeTitle" style="display:none" class="page-title"><h2>' + productName + '</h2></div>' });
        $('productView').insert({'after': bundleOptions });
    }
    $$('.col-right').each(function(el){el.id='rightCOL'});
    new Effect.SlideUp('productView', { duration: 0.8 });
    new Effect.SlideUp('rightCOL', { duration: 0.8 });
    new Effect.SlideDown(bundleOptions, { duration: 0.8 });
    $('customizeTitle').show();
    bCheck == 0;
}
function closeCustomize() {
    $('customizeTitle').hide();
    new Effect.SlideDown('productView', { duration: 0.8 });
    new Effect.SlideDown('rightCOL', { duration: 0.8 });
    new Effect.SlideUp(bundleOptions, { duration: 0.8 });
    bCheck == 0;    
}

function tabsActivate(tId) {
    $(tId).addClassName('tab-list');
    var aTabsNames = $(tId).getElementsBySelector('dt.tab');
    var aTabsContent = $(tId).getElementsBySelector('dd.tab-container');
    var eTabActiveName = aTabsNames.first();
    aTabsNames.first().addClassName('first');
    aTabsNames.last().addClassName('last');
    toggleTabs(eTabActiveName);
    
    aTabsNames.each(function(name) {
        name.onclick = function() {
            if (eTabActiveName != this) {
                eTabActiveName = this;
                toggleTabs(eTabActiveName);
            };
        };
    });
    
    function toggleTabs(eTabActiveName) {
       aTabsNames.each(function(name,i) {
            if (name==eTabActiveName) {
                name.addClassName('active');
                name.style.zIndex = aTabsNames.length + 2;                
                eTabActiveContent=name.next('dd');
                }
            else {
                name.removeClassName('active');
                name.style.zIndex = aTabsNames.length + 1 - i;                
            };
          });
        aTabsContent.each(function(tab) {
            if (tab==eTabActiveContent) {
                tab.show();
                tab.parentNode.style.height = tab.getHeight() + 'px';
                }
            else {
                tab.hide();
                }    
          });
        };        
}