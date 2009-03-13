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
         this.check = 0;
         this.intervalDuration = 2000;
         this.interval = null;
         this.onElementMouseOut = this.handleMouseOut.bindAsEventListener(this);
         this.onElementMouseOver = this.handleMouseOver.bindAsEventListener(this);
         
         this.element.observe('mouseout', this.onElementMouseOut);
         this.element.observe('mouseover', this.onElementMouseOver);
         
     },
     handleMouseOut: function (evt) {
        if(!$(this.container.id).hasClassName('process')) {
            this.interval = setTimeout(this.hideElement.bind(this), this.intervalDuration);
        }
     },
     
     handleMouseOver: function (evt) {
        if (this.check==0 && !$(this.container.id).hasClassName('process') )  {
            this.container.parentNode.style.zIndex=992;
            new Effect.SlideDown(this.container.id, { duration: 0.5 });
            this.check=1;
        }
        if (this.interval !== null) {
             clearTimeout(this.interval);
             this.interval = null;
        }
     },
     
     hideElement: function () {
     
        if (!$(this.container.id).hasClassName('process')) {     
            new Effect.SlideUp(this.container.id, { duration: 0.5,
                beforeStart: function(effect) {$( effect.element.id ).addClassName('process')}, 
                afterFinish: function(effect) {$( effect.element.id ).removeClassName('process');} 
                });
        }
        if (this.interval !== null) {
            clearTimeout(this.interval);
            this.interval = null;
        }
        this.container.parentNode.style.zIndex=1;
        this.check = 0;
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
     },
     end: function () {
         new Effect.SlideDown('productView', { duration: 0.8 });
         new Effect.SlideDown('rightCOL', { duration: 0.8 });
         if (this.options) {
            new Effect.SlideUp(this.options, { 
                duration: 0.8,
                afterFinish: function () { Enterprise.BundleSummary.exitSummary() }
                });
         }
         this.title.hide();
     }
}; 

Enterprise.BundleSummary = {
    initialize: function () {
        this.summary = $('bundleSummary');
        this.summaryContainer = this.summary.getOffsetParent();
        this.summaryContainer.style.top = '0';
        this.summaryStartY = this.summary.positionedOffset().top;
        this.summaryStartX = this.summary.positionedOffset().left;
        this.summary.style.left = this.summaryStartX;
        this.onDocScroll = this.handleDocScroll.bindAsEventListener(this);       
        this.GetScroll = setInterval(this.onDocScroll,1100);   
    },
    
    handleDocScroll: function () {
        if (this.summaryContainer.viewportOffset().top < 10) {
        
              new Effect.Move(this.summary, { 
                    x: this.summaryStartX, 
                    y: -(this.summaryContainer.viewportOffset().top)+15, 
                    mode: 'absolute'
                });

        } else {
             new Effect.Move(this.summary, { 
                    x: this.summaryStartX, 
                    y: this.summaryStartY, 
                    mode: 'absolute'
                });
        }
    },
    
    exitSummary: function () {
        clearInterval(this.GetScroll);       
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
