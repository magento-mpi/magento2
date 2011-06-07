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
 * @category    design
 * @package     default_iphone
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

document.observe("dom:loaded", function() {
    var sliderPosition = 0;
    
    $$("#nav-container ul").each(function(ul) { ul.style.width = document.body.offsetWidth + "px"; });
    
    $$("#nav a").each(function(sliderLink) {
        if (sliderLink.next(0) !== undefined) {
            sliderLink.clonedSubmenuList = sliderLink.next(0);
            
            sliderLink.observe('click', function() {
                if (!this.clonedSubmenuList.firstDescendant().hasClassName('subcategory-header')) {
                    var subcategoryHeader = new Element('li', {'class': 'subcategory-header'});
                    subcategoryHeader.insert({
                        top: new Element('button', {'class': 'previous-category'}).update("Back"),
                        bottom: this.innerHTML
                    });
                    this.clonedSubmenuList.insert({
                        top: subcategoryHeader
                    });
                    
                    this.clonedSubmenuList.firstDescendant().firstDescendant().observe('click', function() {
                        $("nav-container").setStyle({"-webkit-transform" : "translate3d(" + (document.body.offsetWidth + sliderPosition) + "px, 0, 0)"});
                        sliderPosition = sliderPosition + document.body.offsetWidth;
                        setTimeout(function() { $$("#nav-container > ul:last-child")[0].remove(); }, 250)
                    });
                };
                
                $("nav-container").insert(this.clonedSubmenuList);
                $("nav-container").setStyle({"-webkit-transform" : "translate3d(" + (sliderPosition - document.body.offsetWidth) + "px, 0, 0)"});
                
                sliderPosition = sliderPosition - document.body.offsetWidth;
                event.preventDefault();
            });
        };
    });
});
