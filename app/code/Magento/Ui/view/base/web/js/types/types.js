/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    'jquery',
    'Magento_Ui/js/lib/class',
    'Magento_Ui/js/lib/registry/registry'
], function(_, $, Class, registry) {
    'use strict';

    return Class.extend({
        initialize: function(types){
            this.types = {};

            this.set(types);
        },

        set: function(types){
            _.each(types, function(data, type){
                this.types[type] = this.flatten(data);
            }, this);
        },

        get: function(type){
            return this.types[type];
        },

        flatten: function(data){
            var result = {};

            if(!Array.isArray(data)){
                return data;
            }

            data.forEach(function(item){
                if(typeof item === 'string'){
                    item = this.get(item);
                }

                $.extend(true, result, item);
            }, this);

            return result;
        }
    });
});