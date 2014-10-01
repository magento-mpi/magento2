/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    './abstract',
    'mage/utils',
    'underscore'
], function (AbstractElement, utils, _) {
    'use strict';

    var defaults = {
        meta: {
            size: 10
        }
    };

    return AbstractElement.extend({

        /**
         * Invokes initialize method of AbstractElement class and initializes properties of instance.
         * @param {Object} config - form element configuration
         */
        initialize: function (config, value) {
            var initialize = AbstractElement.__super__.initialize;

            _.extend(config.meta, defaults.meta);

            this.initOptions(config.meta.options);

            initialize.call(this, config, this.formatValue(value));
        },

        initOptions: function (options) {
            this.options = this.formatOptions(options);
        },

        formatOptions: function (options) {
            return _.map(options, function (label, value) {
                return {
                    label: label,
                    value: value
                };
            });
        },

        formatValue: function (value) {
            var result = [],
                indexedOptions = _.indexBy(this.options, 'value');

            if (utils.isArray(value)) {
                result = value.map(function (value) {
                    return {
                        value: value,
                        label: indexedOptions[value]
                    }
                })
            }

            return result;
        }
    });
});