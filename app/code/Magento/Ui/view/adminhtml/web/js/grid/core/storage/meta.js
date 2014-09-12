/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    '_',
    './storage'
], function(_, Storage) {
    'use strict';

    return Storage.extend({
        initialize: function(data) {
            this.data = data || {};

            this.preprocess()
                .format();
        },

        preprocess: function() {
            var data    = this.data,
                fields  = data.fields;

            data.fields = this._fieldsToArray(fields);

            return this;
        },

        format: function(){
            var fields = this.data.fields,
                options;

            fields.forEach(function(field){
                this.applyDefaults(field)
                    .formatOptions(field);
            }, this);

            return this;
        },

        applyDefaults: function(field) {
            var defaults = this.data.defaults;

            if (defaults) {
                _.defaults(field, defaults);
            }

            return this;
        },

        formatOptions: function(field) {
            var result,
                options;

            options = field.options;

            if (options) {
                result = {};

                _.each(options, function(option){
                    result[option.value] = option.label;
                });

                field.options = result;
            }

            return this;
        },

        getVisible: function(){
            var fields  = this.data.fields;
            
            return fields.filter(function(field){
                return field.visible;
            });
        },

        _fieldsToArray: function(fields){
            return _.map(fields, function(field, id){
                field.index = id;
                
                return field;
            });
        }
    });
});