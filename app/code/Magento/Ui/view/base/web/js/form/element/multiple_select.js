/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    './select',
    'underscore'
], function (Select) {
    'use strict';

    var defaults = {
        size: 10
    };

    return Select.extend({

        /**
         * Invokes initialize method of AbstractElement class and initializes properties of instance.
         * @param {Object} config - form element configuration
         */
        initialize: function (config, value) {
            _.extend(this, defaults);

            Select.prototype.initialize.apply(this, arguments);
        },

        /**
         * Stores element's value to registry by element's path value
         * @param  {*} changedValue - current value of form element
         */
        store: function (changedValue) {
            var storedValue = [];
            _.each(changedValue, function(option, index){
                storedValue.push(option.value);
            });
            this.provider.data.set(this.name, storedValue);
        },

        /**
         * Formats initial multiselect initial value (array of primitives) to
         *     array of objects based on options
         *     
         * @param  {Array} value - array of primitives
         * @param  {Array} options
         * @return {Array} - array of objects (references to options' objects)
         */
        formatValue: function (value, options) {
            var formattedValue = [],
                indexedOptions = _.indexBy(options, 'value');

            if (_.isArray(value)) {
                formattedValue = value.map(function (value) {
                    return indexedOptions[value];
                });
            }

            return formattedValue;
        }
    });
});