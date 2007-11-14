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
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
var varienLoader = new Class.create();

varienLoader.prototype = {
    initialize : function(caching){
        this.callback= false;
        this.cache   = $H();
        this.caching = caching || false;
        this.url     = false;
    },

    getCache : function(url){
        if(this.cache[url]){
            return this.cache[url]
        }
        return false;
    },

    load : function(url, params, callback){
        this.url      = url;
        this.callback = callback;

        if(this.caching){
            var transport = this.getCache(url);
            if(transport){
                this.processResult(transport);
                return;
            }
        }

        new Ajax.Request(url,{
            method: 'post',
            parameters: params || {},
            onComplete: this.processResult.bind(this),
            onFailure: this._processFailure.bind(this)
        });
    },

    _processFailure : function(transport){
        location.href = BASE_URL;
    },

    processResult : function(transport){
        if(this.caching){
            this.cache[this.url] = transport;
        }
        if(this.callback){
            this.callback(transport.responseText);
        }
    }
}

if (!window.varienLoaderHandler)
    var varienLoaderHandler = new Object();

varienLoaderHandler.handler = {
    onCreate: function(request) {
        if(request.options.loaderArea===false){
            return;
        }
        if(request && request.options.loaderArea){
            Position.clone($(request.options.loaderArea), $('loading-mask'), {offsetLeft:-2});
            toggleSelectsUnderBlock($('loading-mask'), false);
            Element.show('loading-mask');
        }
        else{
            Element.show('loading-process');
        }
    },

    onComplete: function() {
        if(Ajax.activeRequestCount == 0) {
            Element.hide('loading-process');
            toggleSelectsUnderBlock($('loading-mask'), true);
            Element.hide('loading-mask');
        }
    }
};

function toggleSelectsUnderBlock(block, flag){
    if(Prototype.Browser.IE){
        var selects = document.getElementsByTagName("select");
        for(var i=0; i<selects.length; i++){
            /**
             * @todo: need check intersection
             */
            if(flag){
                if(selects[i].needShowOnSuccess){
                    selects[i].needShowOnSuccess = false;
                    Element.show(selects[i])
                }
            }
            else{
                if(Element.visible(selects[i])){
                    Element.hide(selects[i]);
                    selects[i].needShowOnSuccess = true;
                }
            }
        }
    }
}

Ajax.Responders.register(varienLoaderHandler.handler);
