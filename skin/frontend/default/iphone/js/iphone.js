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
    
    var Carousel = Class.create({
       initialize: function (carousel, options) { 
           this.options  = Object.extend({
              visibleElements: 3,
              threshold: {
                  x: 30,
                  y: 20
              },
              preventDefaultEvents: false
           }, options || {});
           
           this.carousel = $(carousel);
           this.items    = this.carousel.select('.carousel-items')[0];
           this.itemPos  = 0;
           this.itemsLength = this.items.childElements().size();
           this.lastItemPos = (this.itemsLength-this.options.visibleElements) * 100/this.options.visibleElements;
           this.screens  = Math.ceil(this.itemsLength/this.options.visibleElements);
           this.counter  = this.carousel.insert(new Element('div', {'class' : 'counter'})).select('.counter')[0];
           this.prevButton = carousel.select('.prev')[0];
           this.nextButton = carousel.select('.next')[0];
           this.originalCoord = { x: 0, y: 0 };
           this.finalCoord    = { x: 0, y: 0 };
       },
       init: function () {
           this.carousel.wrap('div', { 'class' : 'carousel-wrap' });
           if (this.screens > 1) {
               for (var i = 0; i < this.screens; i++) {
                   if (i === 0) {
                       this.counter.insert(new Element('span', {'class': 'active'}));
                   } else {
                       this.counter.insert(new Element('span'));
                   }
               };
           };
           this.nextButton.observe('click', this.moveRight.bind(this));
           this.prevButton.observe('click', this.moveLeft.bind(this));
           this.items.observe('touchstart', this.touchStart.bind(this));
           this.items.observe('touchmove', this.touchMove.bind(this));
           this.items.observe('touchend', this.touchEnd.bind(this));
        },
        moveRight: function () {
            if(Math.abs(this.itemPos) < this.lastItemPos) {
                this.itemPos -= 100/this.options.visibleElements;
                this.items.setStyle({
                    'position': 'relative',
                    '-webkit-transform': 'translateX(' + this.itemPos + '%)'
                });
            
                if (Math.abs(this.itemPos) >= this.lastItemPos) {
                    this.nextButton.addClassName('disabled');
                }
            
                if (this.prevButton.hasClassName('disabled')) {
                    this.prevButton.removeClassName('disabled');
                };
                this.counter.select('.active')[0].removeClassName('active').next().addClassName('active');
            }
        },
        moveLeft: function () {
            if (this.itemPos !== 0) {
                this.itemPos += 100/this.options.visibleElements;
                this.items.setStyle({
                    'position': 'relative',
                    '-webkit-transform': 'translateX(' + this.itemPos + '%)'
                });
        
                if(this.itemPos === 0) {
                    this.prevButton.addClassName('disabled');
                };
        
                if (this.nextButton.hasClassName('disabled')) {
                    this.nextButton.removeClassName('disabled');
                };
                this.counter.select('.active')[0].removeClassName('active').previous().addClassName('active');
            }
        },
        touchStart: function (e) {
            this.originalCoord.x = event.targetTouches[0].pageX;
            this.originalCoord.y = event.targetTouches[0].pageY;
        },
        touchMove: function (e) {
            if (this.options.preventDefaultEvents) {
                e.preventDefault();
            }
            this.finalCoord.x = e.targetTouches[0].pageX;
            this.finalCoord.y = e.targetTouches[0].pageY;
        },
        touchEnd: function (e) {
            var changeY = this.originalCoord.y - this.finalCoord.y,
                changeX;
            if (changeY < this.options.threshold.y && changeY > (this.options.threshold.y * -1)) {
                changeX = this.originalCoord.x - this.finalCoord.x;
                if(changeX > this.options.threshold.x) {
                    this.moveRight();
                }
                if(changeX < this.options.threshold.x * -1) {
                    this.moveLeft();
                }
            }
        }
    });
    
    var upSellCarousel = new Carousel($$('.carousel')[0], {
        visibleElements: 3,
        preventDefaultEvents: true
    }).init();

});
