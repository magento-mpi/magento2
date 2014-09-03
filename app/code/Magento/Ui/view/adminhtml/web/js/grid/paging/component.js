define([
    './paging',
    'Magento_Ui/js/registry/registry'
], function(Paging, registry) {
    'use strict';
    
    return function(el, config, initial) {
        var namespace = config.namespace;

        registry.get( namespace + ':storage', function( storage ){
            config.storage = storage;

            var paging = new Paging(config, initial);

            registry.set( namespace + ':paging', paging );
        });
    }
});