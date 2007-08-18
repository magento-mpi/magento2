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
            onComplete: this.processResult.bind(this)
        });
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
        if(request && request.options.loaderArea){
            Position.clone($(request.options.loaderArea), $('loading-mask'));
            Element.show('loading-mask');
        }
        else{
            Element.show('loading-process');
        }
    },

    onComplete: function() {
        if(Ajax.activeRequestCount == 0) {
            Element.hide('loading-process');
            Element.hide('loading-mask');
        }
    }
};

Ajax.Responders.register(varienLoaderHandler.handler);
