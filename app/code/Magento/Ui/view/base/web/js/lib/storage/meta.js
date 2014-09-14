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

    function hasComplexValue(target, valueKey) {
        var result = false,
            key,
            object;


        for (key in target) {
            object = target[key];

            if (typeof object[valueKey] === 'object') {
                result = true;
                break;
            }
        }

        return result;
    }

    /**
     * Recursively loops over object's properties and converts it to array ignoring keys.
     * If typeof 'value' properties is 'object', creates 'items' property and assigns
     * execution of nestedObjectToArray on 'value' to it.
     * If typeof 'value' key is not an 'object', is simply writes an object itself to result array. 
     * @param  {Object} obj
     * @return {Array} result array
     */
    function nestedObjectToArray(obj, valueKey) {
        var target,
            items = [];

        for (var prop in obj) {

            target = obj[prop];
            if (typeof target[valueKey] === 'object') {

                target.items = nestedObjectToArray(target[valueKey], valueKey);
                delete target[valueKey];
            }
            items.push(target);
        }

        return items;
    }

    return Storage.extend({
        initialize: function(data) {
            this.data = data || {};

            this.initFields()
                .initColspan();
        },

        initFields: function(){
            var data    = this.data,
                fields  = data.fields;

            fields = this._fieldsToArray(fields);

            fields.forEach(this._processField, this);

            data.fields = fields;

            return this;
        },

        initColspan: function(){
            var visible = this.getVisible();

            this.data.colspan = visible.length;

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
                options,
                isNested;

            options = field.options;
            isNested = hasComplexValue(options, 'value');

            if (options) {
                result = isNested ? nestedObjectToArray(options, 'value') : {};

                if (!isNested) {
                    _.each(options, function(option){
                        result[option.value] = option.label;
                    });    
                }
                
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
        },

        _processField: function(field){
            this.applyDefaults(field)
                .formatOptions(field);
        }
    });
});