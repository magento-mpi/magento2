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


if (!window.Enterprise) {
    window.Enterprise = {};
}
Enterprise.TopCart= {
    initialize: function (container) {
        this.container = $(container);
        this.element = this.container.up(0);
        this.elementHeader = this.container.previous(0);
        this.intervalDuration = 4000;
        this.interval = null;
        this.onElementMouseOut = this.handleMouseOut.bindAsEventListener(this);
        this.onElementMouseOver = this.handleMouseOver.bindAsEventListener(this);
        this.onElementMouseClick = this.handleMouseClick.bindAsEventListener(this);

        this.element.observe('mouseout', this.onElementMouseOut);
        this.element.observe('mouseover', this.onElementMouseOver);
        this.elementHeader.observe('click', this.onElementMouseClick);
     
    },

    handleMouseOut: function (evt) {
        if($(this.elementHeader).hasClassName('expanded')) {
            this.interval = setTimeout(this.hideCart.bind(this), this.intervalDuration);
        }
    },

    handleMouseOver: function (evt) {
        if (this.interval !== null) {
             clearTimeout(this.interval);
             this.interval = null;
        }
    },

    handleMouseClick: function (evt) {
        if (!$(this.elementHeader).hasClassName('expanded') && !$(this.container.id).hasClassName('process') )  {
            this.showCart();
        }
        else {
            this.hideCart();
        }
    },     
     
    showCart: function (timePeriod) {
        this.container.parentNode.style.zIndex=992;
        new Effect.SlideDown(this.container.id, { duration: 0.5,
            beforeStart: function(effect) {$( effect.element.id ).addClassName('process');}, 
            afterFinish: function(effect) {$( effect.element.id ).removeClassName('process'); } 
            });
        $(this.elementHeader).addClassName('expanded');
        if(timePeriod) {
            this.timePeriod = timePeriod*1000;
            this.interval = setTimeout(this.hideCart.bind(this), this.timePeriod);
        }        
    },
     
    hideCart: function () {

        if (!$(this.container.id).hasClassName('process') && $(this.elementHeader).hasClassName('expanded')) {     
            new Effect.SlideUp(this.container.id, { duration: 0.5,
                beforeStart: function(effect) {$( effect.element.id ).addClassName('process');}, 
                afterFinish: function(effect) {
                    $( effect.element.id ).removeClassName('process');
                    effect.element.parentNode.style.zIndex=1;
                    } 
                });
        }
        if (this.interval !== null) {
            clearTimeout(this.interval);
            this.interval = null;
        }
        $(this.elementHeader).removeClassName('expanded');
    }
};

Enterprise.Bundle = {
     initialize: function () {
         this.options = $('options-container');
         
         if (this.options) {
             this.options.hide();
             this.options.addClassName('bundleProduct');
         }
         this.title = $('customizeTitle');
         this.summary = $('bundleSummary').hide();
     },
     start: function () {
         $$('.col-right').each(function(el){el.id='rightCOL'});
         new Effect.SlideUp('productView', { duration: 0.8 });
         new Effect.SlideUp('rightCOL', { duration: 0.8 });
         if (this.options) {
            new Effect.SlideDown(this.options, { 
                duration: 0.8, 
                afterFinish: function () { Enterprise.BundleSummary.initialize() }
            });
         }
         this.title.show();
         $$('.col-main').each(function(el){el.addClassName('with-bundle')});
     },
     end: function () {
         new Effect.SlideDown('productView', { duration: 0.8 });
         new Effect.SlideDown('rightCOL', { duration: 0.8 });
         if (this.options) {
            new Effect.SlideUp(this.options, { 
                duration: 0.8,
                afterFinish: function () { 
                    Enterprise.BundleSummary.exitSummary();
                    $$('.col-main').each(function(el){el.removeClassName('with-bundle')});
                    }
                });
         }
         this.title.hide();
     }
}; 

Enterprise.BundleSummary = {
    initialize: function () {
        this.summary = $('bundleSummary');
        this.summary.show();
        this.summaryContainer = this.summary.getOffsetParent();
        this.summary.style.top = '31px';
        this.summary.style.right = '-214px';

        this.summaryStartY = this.summary.positionedOffset().top;
        this.summaryStartX = 693;
        this.onDocScroll = this.handleDocScroll.bindAsEventListener(this);      
        this.GetScroll = setInterval(this.onDocScroll,1100);   
    },
    
    handleDocScroll: function () {
        if (this.summaryContainer.viewportOffset().top < 10) {
        
              new Effect.Move(this.summary, { 
                    x: 693, 
                    y: -(this.summaryContainer.viewportOffset().top)+31, 
                    mode: 'absolute'
                });

        } else {
             new Effect.Move(this.summary, { 
                    x: 693, 
                    y: this.summaryStartY, 
                    mode: 'absolute'
                });
        }
    },
    
    exitSummary: function () {
        clearInterval(this.GetScroll);
        this.summary.hide();        
    } 
};

Enterprise.Tabs = Class.create();
Object.extend(Enterprise.Tabs.prototype, {
    initialize: function (container) {
        this.container = $(container);
        this.container.addClassName('tab-list');
        this.tabs = this.container.select('dt.tab');
        this.activeTab = this.tabs.first();
        this.tabs.first().addClassName('first');
        this.tabs.last().addClassName('last');
        this.onTabClick = this.handleTabClick.bindAsEventListener(this);
        for (var i = 0, l = this.tabs.length; i < l; i ++) {
            this.tabs[i].observe('click', this.onTabClick);
        }
        this.select();
    },
    handleTabClick: function (evt) {
        this.activeTab = Event.findElement(evt, 'dt');
        this.select();
    }, 
    select: function () {
        for (var i = 0, l = this.tabs.length; i < l; i ++) {
            if (this.tabs[i] == this.activeTab) {
                this.tabs[i].addClassName('active');
                this.tabs[i].style.zIndex = this.tabs.length + 2;
                /*this.tabs[i].next('dd').show();*/
                new Effect.Appear (this.tabs[i].next('dd'), { duration:0.5 });
                this.tabs[i].parentNode.style.height=this.tabs[i].next('dd').getHeight() + 15 + 'px';
            } else {
                this.tabs[i].removeClassName('active');
                this.tabs[i].style.zIndex = this.tabs.length + 1 - i;
                this.tabs[i].next('dd').hide();
            }
        }
    }
});


Enterprise.Slider = Class.create();

Object.extend(Enterprise.Slider.prototype, {
    initialize: function (container, config) {
        this.container = $(container);
        this.config = {
            panelCss: 'slider-panel',
            sliderCss: 'slider',
            itemCss: 'slider-item',
            slideButtonCss: 'slide-button',
            slideButtonInactiveCss: 'inactive',
            forwardButtonCss: 'forward',
            backwardButtonCss: 'backward',
            pageSize: 2,
            slideDuration: 1.0,
            slideDirection: 'horizontal'
        };
        
        Object.extend(this.config, config || {});
        
        this.items = this.container.select('.' + this.config.itemCss);
        this.isPlaying = false;
        this.isAbsolutized = false;
        this.totalPages = Math.ceil(this.items.length / this.config.pageSize);
        this.currentPage = 1;
        this.onClick = this.handleClick.bindAsEventListener(this);
        this.sliderPanel = this.container.down('.' + this.config.panelCss);
        this.slider =  this.sliderPanel.down('.' + this.config.sliderCss);
        this.container.select('.' + this.config.slideButtonCss).each(
            this.initializeHandlers.bind(this)
        );
        this.updateButtons();
    },
    initializeHandlers: function (element) {
        if (element.hasClassName(this.config.forwardButtonCss) ||
            element.hasClassName(this.config.backwardButtonCss)) {
            element.observe('click', this.onClick);
        }
    },
    handleClick: function (evt) {
        var element = Event.element(evt);
        if (!element.hasClassName(this.config.slideButtonCss)) {
            element = element.up('.' + this.config.slideButtonCss);
        }
        
        if (!element.hasClassName(this.config.slideButtonInactiveCss)) {
           element.hasClassName(this.config.forwardButtonCss) || this.backward();
           element.hasClassName(this.config.backwardButtonCss) || this.forward();
        }
        Event.stop(evt);
    },
    updateButtons: function () {
        var buttons = this.container.select('.' + this.config.slideButtonCss);
        for (var i=0, l=buttons.length; i < l; i++) {
            if (buttons[i].hasClassName(this.config.backwardButtonCss)) {
                this.currentPage != 1 || buttons[i].addClassName(this.config.slideButtonInactiveCss);
                this.currentPage == 1 || buttons[i].removeClassName(this.config.slideButtonInactiveCss);
            } else if (buttons[i].hasClassName(this.config.forwardButtonCss)) {
                this.currentPage < this.totalPages || buttons[i].addClassName(this.config.slideButtonInactiveCss);
                this.currentPage == this.totalPages || buttons[i].removeClassName(this.config.slideButtonInactiveCss);
            }
        }
    },
    absolutize: function () {
        if (!this.isAbsolutized) {
            this.isAbsolutized = true;
            var dimensions = this.sliderPanel.getDimensions();
            this.sliderPanel.setStyle({
                height: dimensions.height + 'px',
                width: dimensions.width + 'px'
            });
            this.slider.absolutize();
        }
    },
    forward: function () {
        if (this.currentPage < this.totalPages) {
            this.slide(this.currentPage + 1);
        }
    },
    backward: function () {
        if (this.currentPage > 0) {
            this.slide(this.currentPage - 1);
        }
    },
    slide: function (page) {
        
        if (page == this.currentPage 
            || this.isPlaying) {
            return;
        }       
        this.absolutize();
        this.currentPage = page;
        var effectConfig = {
            afterFinish: this.effectEnds.bind(this),
            beforeStart: this.effectStarts.bind(this),
            duration: this.config.slideDuration
        };
        if (this.config.slideDirection == 'horizontal') {
            effectConfig.x = this.getSlidePosition(page).left;
        } else {
            effectConfig.y = this.getSlidePosition(page).top;
        }
        new Effect.Move(this.slider, effectConfig);
        this.updateButtons();
    },
    effectStarts: function () {
        this.isPlaying = true;
    },
    effectEnds: function () {
        this.isPlaying = false;
    },
    getSlidePosition: function (page) {
        var item = this.findItem(page * this.config.pageSize - this.config.pageSize, this.config.pageSize);
        var itemOffset = {left:0, top:0};
        
        itemOffset.left = -(item.cumulativeOffset().left 
                       -  this.slider.cumulativeOffset().left + this.slider.offsetLeft);
        itemOffset.top = -(item.cumulativeOffset().top 
                       -  this.slider.cumulativeOffset().top + this.slider.offsetTop);
        return itemOffset;
    },
    findItem: function (offset, total) {
        var last = false;
        
        for (var i = 0, l = this.items.length; 
             i <= offset && i + total <= l;
             i ++) 
        {
             last = this.items[i];
        }
        return last;
    }
});

function popUpMenu(element,trigger) {
        var iDelay = 1500;
        var new_popup = 0;
        var sTempId = 'popUped';
        if (document.getElementById(sTempId)) {
            var eTemp = document.getElementById(sTempId);
            eTemp.hide();
            eTemp.id = sNativeId;
            clearTimeout(tId);
            document.onclick = null;
        }
            
        sNativeId = 'popId-'+element.parentNode.id;

        var el = $(sNativeId);

        el.id = sTempId;

        if (eTemp && el == eTemp) {
            hideElement();
        } else {
            $(sTempId).getOffsetParent().style.zIndex = 994;
//          el.show();
            new Effect.Appear (el, { duration:0.3 });
            tId=setTimeout("hideElement()",2*iDelay);        
        }
        new_popup = 1;    
        document.onclick = function() {
            if (!new_popup) {
                hideElement();
                document.onclick = null;
            }
            new_popup = 0;    
        }
        
        el.onmouseout = function() {
            if ($(sTempId)) {    
                $(sTempId).addClassName('faded');
                tId=setTimeout("hideElement()",iDelay);
            }
        }
        
        el.onmouseover = function() {
            if ($(sTempId)) {    
                $(sTempId).removeClassName('faded');
                clearTimeout(tId);
            }
        }
        
        hideElement = function() {    
            //el.hide();
            new Effect.Fade (el, { duration:0.3 });
            el.getOffsetParent().style.zIndex = 1;
            el.id = sNativeId;
            if (tId) {clearTimeout(tId);}
        }
}

