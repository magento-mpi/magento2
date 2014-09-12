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
            var fields,
                fieldsMap,
                result;

            fields      = this.data.fields;
            fieldsMap   = this._sortedFieldsMap(fields);
            result      = this._fieldsToArray(fields, fieldsMap);

            this.data.fields = result;

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

        _sortedFieldsMap: function(fields){
            var fieldsMap;

            fieldsMap = _.map(fields, function(field, index) {
                return {
                    pos: field.position,
                    index: index
                };
            });

            fieldsMap.sort(function(a, b) {
                return a.pos - b.pos;
            });

            return fieldsMap;
        },

        _fieldsToArray: function(fields, fieldsMap){
            var field,
                index;

            return fieldsMap.map(function(item) {
                index = item.index;
                field = fields[index];

                delete field.position;

                field.index = index;

                return field;
            });
        }
    });
});