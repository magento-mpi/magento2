define([
    '../class',
    '../events',
    '../utils',
    'ko'
], function(Class, EventBus, utils, ko) {


    return Class.extend({

        mixins: [EventBus],

        def: function(path, value) {
            utils.setValueByPathIn(this, path, ko.observable(value));

            return this;
        },

        defArray: function(path, arr) {
            utils.setValueByPathIn(this, path, ko.observableArray(arr));

            return this;
        },

        observable: function( obj ){
            var key,
                value,
                method;

            for( key in obj ){
                value   = obj[ key ];
                method  = Array.isArray( value ) ? 'observableArray' : 'observable';

                utils.setValueByPathIn( this, key, ko[ method ]( value ) );
            }
        }
    });
});