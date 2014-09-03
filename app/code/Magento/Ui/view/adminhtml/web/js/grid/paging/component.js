define([
    './paging',
    'Magento_Ui/js/framework/ko/view',
    'Magento_Ui/js/registry/registry'
], function(Paging, View, registry) {
    'use strict';
    
    return function(el, config, initial) {
        
        registry.get(config.namespace + ':storage', function(storage){
            config.storage = storage;

            var paging = new Paging(config, initial);

            View.bind(el, paging); 
        });
    }
});