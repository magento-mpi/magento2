define([
    './sorting',
    'Magento_Ui/js/lib/registry/registry'
], function(Sorting, registry) {
    'use strict';
    
    return function(el, data) {
        var config = data.config,
            namespace = config.namespace;

        registry.get( namespace + ':storage', function( storage ){
            var sorting;

            config.storage = storage;

            sorting = new Sorting( config );

            registry.set( namespace + ':sorting', sorting );
        });
    }
});