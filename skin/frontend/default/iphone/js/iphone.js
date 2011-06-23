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

 // Homepage categories and subcategories slider
document.observe("dom:loaded", function() {
    
    Event.observe(window, 'orientationchange', function() {
        $$("#nav-container ul").each(function(ul) { ul.style.width = document.body.offsetWidth + "px"; });
    });
    
    var sliderPosition = 0;
    
    $$("#nav-container ul").each(function(ul) { ul.style.width = document.body.offsetWidth + "px"; });
    
    $$("#nav a").each(function(sliderLink) {
        if (sliderLink.next(0) !== undefined) {
            sliderLink.href = "#";
            sliderLink.clonedSubmenuList = sliderLink.next(0);
            
            sliderLink.observe('click', function() {
                if (!this.clonedSubmenuList.firstDescendant().hasClassName('subcategory-header')) {
                    var subcategoryHeader = new Element('li', {'class': 'subcategory-header'});
                    subcategoryHeader.insert({
                        top: new Element('button', {'class': 'previous-category'}).update("Back").wrap('div', {'class':'button-wrap'}),
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


    //iPhone header menu
    $('menu').on('click', 'dt.dropdown a', function(e, elem) {
        var parent = elem.up();
        if (parent.hasClassName('active')) {
            parent.removeClassName('active');
            $$('#menu dd').each(function(elem) {
                elem.hide();
            })
        }
        else {
            $$('#menu dt').each(function (elem){
                elem.removeClassName('active');
                elem.next('dd').hide();
            });
            parent.addClassName('active');
            parent.next().show();
        };
        e.preventDefault();
    });
    
    //iPhone header menu switchers
    var curLang = $$('#language-switcher li.selected a')[0].innerHTML,
        curStore = $$('#store-switcher li.selected a')[0].innerHTML;
    
    $('current-language').update(curLang);
    $('current-store').update(curStore);
    
    $$('#language-switcher > a')[0].observe('click', function (e){
        this.next().toggle();
        e.preventDefault();
    });
    
    $$('#store-switcher > a')[0].observe('click', function (e){
        this.next().toggle();
        e.preventDefault();
    });
    
    //Slider
    
    var carousel = $$('.carousel')[0],
        carouselItems = carousel.select('.carousel-items')[0],
        itemsWidth = carouselItems.getWidth(),
        itemsLength = carouselItems.childElements().size(),
        screens = Math.ceil(itemsLength/3),
        counter = carousel.select('.counter')[0],
        itemPos = 0,
        lastItemPos = (itemsLength-3) * 100/3,
        prevButton = carousel.select('.prev')[0],
        nextButton = carousel.select('.next')[0];
    
    carouselItems.wrap('div', {'class': 'carousel-wrap'});
    
    if (screens > 1) {
    
        for (var i = 0; i < screens; i++) {
            if (i === 0) {
                counter.insert(new Element('span', { 'class': 'active' }));
            } else {
                counter.insert(new Element('span'));
            };
        }
    
        prevButton.observe('click', function () {
            if (itemPos !== 0) {
                itemPos += 100/3;
                carouselItems.setStyle({
                    'position': 'relative',
                    '-webkit-transform': 'translateX(' + itemPos + '%)'
                });
            
                if(itemPos === 0) {
                    prevButton.addClassName('disabled');
                };
            
                if (nextButton.hasClassName('disabled')) {
                    nextButton.removeClassName('disabled');
                };
                counter.select('.active')[0].removeClassName('active').previous().addClassName('active');
            };
        });
    
        nextButton.observe('click', function () {
            if(Math.abs(itemPos) < lastItemPos) {
                itemPos -= 100/3;
                carouselItems.setStyle({
                    'position': 'relative',
                    '-webkit-transform': 'translateX(' + itemPos + '%)'
                });
            
                if (Math.abs(itemPos) >= lastItemPos) {
                    nextButton.addClassName('disabled');
                }
            
                if (prevButton.hasClassName('disabled')) {
                    prevButton.removeClassName('disabled');
                };
                counter.select('.active')[0].removeClassName('active').next().addClassName('active');
            };
        });

    } else {
        carousel.select('.controls')[0].hide();
    }

});
