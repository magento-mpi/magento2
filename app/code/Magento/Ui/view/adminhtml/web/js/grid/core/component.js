define([
    'Magento_Ui/js/lib/registry/registry'
], function(registry){
    'use strict';

    function init( config, el, data ){
        var settings    = data.config,
            namespace   = settings.namespace;

        registry.get(namespace + ':storage', function(storage){
            var component,
                name;

            settings.storage = storage;

            component   = new config.constr( settings );
            name        = namespace + ':' + config.name;

            registry.set( name, component );
        });
    }

    return function( config ){
        return init.bind( this, config );
    }
});