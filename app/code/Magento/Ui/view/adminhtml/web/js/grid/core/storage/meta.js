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

            this.format();
        },

        format: function(){
            var fields = this.data.fields,
                options;

            fields.forEach(function(field){
                this.applyDefaults(field)
                    .formatOptions(field);
            }, this);
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
        }
    });
});