/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    './abstract',
    'underscore',
    'i18n'
], function (AbstractElement, _, i18n) {
    'use strict';

    var defaults = {
        caption: i18n('Select...'),
        no_caption: false
    };

    return AbstractElement.extend({

        /**
         * Extends instance with defaults, extends config with formatted values 
         *     and options, and invokes initialize method of AbstractElement class.
         * @param {Object} config - form element configuration
         */
        initialize: function (config) {
            _.extend(this, defaults);

            this.extendConfig(config);

            AbstractElement.prototype.initialize.apply(this, arguments);
        },

        /**
         * Rewrites value and options properties of config by formatted ones.
         * @param  {Object} config [description]
         */
        extendConfig: function (config) {
            var options = this.formatOptions(config.options),
                value   = this.formatValue(config.value, options);

            _.extend(config, {
                options: options,
                value: value
            });
        },

        /**
         * Stores element's value to registry by element's path value
         * @param  {*} changedValue - current value of form element
         */
        store: function (changedValue) {
            this.refs.provider.data.set(this.name, changedValue.value);
        },

        /**
         * Formats options to array of {value: '...', label: '...'} objects.
         * @param  {Object} options
         * @return {Array} - formatted options
         */
        formatOptions: function (options) {
            return _.map(options, function (fullValue, index) {
                return {
                    label: fullValue.label,
                    value: fullValue.value
                };
            });
        },

        /**
         * Formats initial value of select by looking up for corresponding
         *     value to options.
         * @param  {Number} value
         * @param  {Array} options
         * @return {Object} - { value: '...', label: '...' }
         */
        formatValue: function (value, options) {
            var formattedValue = value + '',
                indexedOptions = _.indexBy(options, 'value');

            if (formattedValue) {
                formattedValue = indexedOptions[value];
            }

            return formattedValue;
        }
    });
});