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
        var orientation;
        switch(window.orientation){  
            case 0:
            orientation = "portrait";
            break;

            case -90:
            orientation = "landscape";
            break;

            case 90:
            orientation = "landscape";
            break;
        }
        $$("#nav-container ul").each(function(ul) { ul.style.width = document.body.offsetWidth + "px"; });
        
        if (orientation === 'landscape') {
            upSellCarousel.resize(3);
        } else {
            upSellCarousel.resize(2);
        }
        
    });
    
    var sliderPosition = 0,
        last,
        diff;
    
    $$("#nav-container ul").each(function(ul) { ul.style.width = document.body.offsetWidth + "px"; });
    
    if ($$('body')[0].hasClassName('cms-home')) {
        $('home-link').addClassName('disabled');
    }
    
    var homeLink = $('home-link');

    homeLink.observe('click', function (e) {
        if (homeLink.hasClassName('disabled')) {
            e.preventDefault();
        }
    });
    
    $$("#nav a").each(function(sliderLink) {
        if (sliderLink.next(0) !== undefined) {
            sliderLink.href = "#";
            sliderLink.clonedSubmenuList = sliderLink.next(0);
            
            sliderLink.observe('click', function(e) {
                
                homeLink.hasClassName('disabled') ? homeLink.removeClassName('disabled') : '';
                
                if (last) {
                    diff = e.timeStamp - last
                }
                last = e.timeStamp;
                if (diff && diff < 300) {
                    return
                }
                if (!this.clonedSubmenuList.firstDescendant().hasClassName('subcategory-header')) {
                    var subcategoryHeader = new Element('li', {'class': 'subcategory-header'});
                    subcategoryHeader.insert({
                        top: new Element('button', {'class': 'previous-category'}).update("Back").wrap('div', {'class':'button-wrap'}),
                        bottom: this.innerHTML
                    });
                    this.clonedSubmenuList.insert({
                        top: subcategoryHeader
                    });
                    
                    this.clonedSubmenuList.firstDescendant().firstDescendant().observe('click', function(e) {
                        if (last) {
                            diff = e.timeStamp - last
                        }
                        last = e.timeStamp;
                        if (diff && diff < 300) {
                            return
                        }
                        $("nav-container").setStyle({"-webkit-transform" : "translate3d(" + (document.body.offsetWidth + sliderPosition) + "px, 0, 0)"});
                        sliderPosition = sliderPosition + document.body.offsetWidth;
                        setTimeout(function() { $$("#nav-container > ul:last-child")[0].remove(); }, 250)
                    });
                    new NoClickDelay(this.clonedSubmenuList);
                };
                
                $("nav-container").insert(this.clonedSubmenuList);
                $("nav-container").setStyle({"-webkit-transform" : "translate3d(" + (sliderPosition - document.body.offsetWidth) + "px, 0, 0)"});
                
                sliderPosition = sliderPosition - document.body.offsetWidth;
                e.preventDefault();
            });
        };
    });
    
    function NoClickDelay(el) {
        this.element = typeof el == 'object' ? el : document.getElementById(el);
        if( window.Touch ) this.element.addEventListener('touchstart', this, false);
    }

    NoClickDelay.prototype = {
        handleEvent: function(e) {
            switch(e.type) {
                case 'touchstart': this.onTouchStart(e); break;
                case 'touchmove': this.onTouchMove(e); break;
                case 'touchend': this.onTouchEnd(e); break;
            }
        },

        onTouchStart: function(e) {
            this.moved = false;

            this.theTarget = document.elementFromPoint(e.targetTouches[0].clientX, e.targetTouches[0].clientY);
            if(this.theTarget.nodeType == 3) this.theTarget = theTarget.parentNode;
            this.theTarget.className+= ' pressed';

            this.element.addEventListener('touchmove', this, false);
            this.element.addEventListener('touchend', this, false);
        },

        onTouchMove: function(e) {
            this.moved = true;
            this.theTarget.className = this.theTarget.className.replace(/ ?pressed/gi, '');
        },

        onTouchEnd: function(e) {
            e.preventDefault();
            
            this.element.removeEventListener('touchmove', this, false);
            this.element.removeEventListener('touchend', this, false);

            if( !this.moved && this.theTarget ) {
                this.theTarget.className = this.theTarget.className.replace(/ ?pressed/gi, '');
                var theEvent = document.createEvent('MouseEvents');
                theEvent.initEvent('click', true, true);
                this.theTarget.dispatchEvent(theEvent);
            }

            this.theTarget = undefined;
        }
    };
    
    if (document.getElementById('nav')) {
        new NoClickDelay(document.getElementById('nav'));
    }


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
    if( $$('#language-switcher li.selected a')[0] ) {
        var curLang = $$('#language-switcher li.selected a')[0].innerHTML;
        $('current-language').update(curLang);
        
        $$('#language-switcher > a')[0].observe('click', function (e) {
            if ( !this.next().visible() )
                $$('.switcher-options').invoke('hide');
            this.next().toggle().toggleClassName('visible');
            e.preventDefault();
        });
    }
    
    if( $$('#store-switcher li.selected a')[0] ) {
        var curStore = $$('#store-switcher li.selected a')[0].innerHTML;
        $('current-store').update(curStore);
        
        $$('#store-switcher > a')[0].observe('click', function (e) {
            if ( !ithis.next().visible() )
                $$('.switcher-options').invoke('hide');
            this.next().toggle().toggleClassName('visible');
            e.preventDefault();
        });
     }

    //Slider
    
    var Carousel = Class.create({
       initialize: function (carousel, itemsContainer, options) { 
           this.options  = Object.extend({
              visibleElements: 3,
              threshold: {
                  x: 30,
                  y: 40
              },
              preventDefaultEvents: false
           }, options || {});
           
           this.carousel = carousel;
           this.items    = itemsContainer.addClassName('carousel-items');
           this.itemsLength = this.items.childElements().size();
           this.counter  = this.carousel.insert(new Element('div', {'class' : 'counter'})).select('.counter')[0];
           this.controls = carousel.select('.controls')[0];
           this.prevButton = carousel.select('.prev')[0];
           this.nextButton = carousel.select('.next')[0];
           this.originalCoord = { x: 0, y: 0 };
           this.finalCoord    = { x: 0, y: 0 };
           
           this.carousel.wrap('div', { 'class' : 'carousel-wrap' });

           this.nextButton.observe('click', this.moveRight.bind(this));
           this.prevButton.observe('click', this.moveLeft.bind(this));
           this.items.observe('touchstart', this.touchStart.bind(this));
           this.items.observe('touchmove', this.touchMove.bind(this));
           this.items.observe('touchend', this.touchEnd.bind(this));
       },
       init: function () {
           this.itemPos  = 0;
           this.lastItemPos = (this.itemsLength-this.options.visibleElements) * 100/this.options.visibleElements;
           this.itemWidth = 100/this.options.visibleElements + '%';
           this.screens  = Math.ceil(this.itemsLength/this.options.visibleElements);
           
           this.resizeChilds();
           this.drawCounter();
           
           return this;
        },
        resize: function(visibleElements) {
            this.options.visibleElements = visibleElements;
            this.counter.childElements().invoke('remove');
            this.items.setStyle({
                '-webkit-transform': 'translateX(' + 0 + '%)'
            });
            this.prevButton.addClassName('disabled');
            this.nextButton.removeClassName('disabled');
            this.init();
        },
        resizeChilds: function () {
           this.items.childElements().each( function(n) {
              n.setStyle({
                  'width': this.itemWidth
              });
           }, this);
        },
        drawCounter: function () {
            if (this.screens > 1) {
                 if (this.controls)
                     this.controls.show()
                 for (var i = 0; i < this.screens; i++) {
                   if (i === 0) {
                       this.counter.insert(new Element('span', {'class': 'active'}));
                   } else {
                       this.counter.insert(new Element('span'));
                   }
               };
           } else {
               if (this.controls)
                   this.controls.hide();
           }
        },
        moveRight: function () {
            if(Math.abs(this.itemPos) < this.lastItemPos) {
                this.itemPos -= 100/this.options.visibleElements * this.options.visibleElements;
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
                this.itemPos += 100/this.options.visibleElements * this.options.visibleElements;
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
            if (Math.abs(this.finalCoord.y - this.originalCoord.y) > 100) {
            }
            /*
            if (this.controls) {
                this.controls.update(Math.abs(this.finalCoord.y - this.originalCoord.y))
            }
            */
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
    
    if ( $$('.carousel')[0] ) {
        var upSellCarousel = new Carousel($$('.carousel')[0], $$('.carousel-items')[0], {
            visibleElements: 2,
            preventDefaultEvents: true
        }).init();
    }
    
    if ( $$('.product-gallery')[0] ) {
        var galleryCarousel = new Carousel($$('.product-gallery')[0], $$('.product-gallery > ul')[0], {
            visibleElements: 1,
            preventDefaultEvents: false
        }).init();
    }
    
    if ( $$('.product-image li').size() > 1 ) {
        var productGallery = new Carousel($$('.product-image')[0], $$('.product-image ul')[0], {
            visibleElements: 1,
            preventDefaults: false
        }).init();
    }

    // Swipe Functionality
     
    var Swipe = Class.create( Carousel, {
        initialize: function (elem, swipeLeft, swipeRight, options) { 
            this.options  = Object.extend({
                threshold: {
                    x: 30,
                    y: 20
                },
                preventDefaultEvents: false
            }, options || {});
            
            this.elem = elem;
            this.originalCoord = { x: 0, y: 0 };
            this.finalCoord    = { x: 0, y: 0 };
            
            this.elem.observe('touchstart', this.touchStart.bind(this));
            this.elem.observe('touchmove', this.touchMove.bind(this));
            this.elem.observe('touchend', this.touchEnd.bind(this));
            this.moveLeft = swipeRight;
            this.moveRight = swipeLeft;
        }
    });
    
    if ( $$('.c-list')[0] ) {
        $$('.c-list > li').each( function (item) {
            new Swipe(item,
                function() {
                    item.removeClassName('animated').addClassName('end-animation');
                },
                function() {
                    if ( !item.hasClassName('animated') ) {
                        $$('.c-list > li.animated').invoke('removeClassName', 'animated').invoke('addClassName', 'end-animation');
                        item.addClassName('animated').removeClassName('end-animation');
                    }
                }
            );
        });
    }
});
