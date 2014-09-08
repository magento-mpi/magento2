define([], function(){
    'use strict';
    
    var data = {};

    return {
        get: function( elems ){
            var result = [],    
                record;

            elems.forEach(function( elem ){
                record = data[ elem ];

                result.push( record ? record.value : undefined );
            });

            return result;
        },

        set: function( elem, value ){
            var record = data[ elem ] = data[elem] || {};

            record.value = value;
        },

        remove: function( elems ){
            elems.forEach(function( elem ){
                delete data[elem];
            });
        },

        has: function( elems ){
            return elems.every(function( elem ){
                return typeof data[elem] !== 'undefined';
            });
        }
    };
});
