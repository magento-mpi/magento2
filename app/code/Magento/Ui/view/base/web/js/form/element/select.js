/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    './abstract',
    'mage/utils',
    'underscore',
    'i18n'
], function (AbstractElement, utils, _, i18n) {
    'use strict';

    var defaults = {
        caption: i18n('Select...')
    };

    return AbstractElement.extend({

        /**
         * Invokes initialize method of AbstractElement class and initializes properties of instance.
         * @param {Object} config - form element configuration
         */
        initialize: function (config) {
            _.extend(this, defaults);

            this.extendConfig(config);

            AbstractElement.prototype.initialize.apply(this, arguments);
        },

        extendConfig: function (config) {
            var options = this.formatOptions(config.options),
                value   = this.formatValue(config.value, options);

            _.extend(config, {
                options: options,
                value: value
            });
        },

        formatOptions: function (options) {
            return _.map(options, function (label, value) {
                return {
                    label: label,
                    value: value
                };
            });
        },

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