/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    './select',
    'mage/utils'
], function (Select, utils) {
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

        formatValue: function (value, options) {
            var formattedValue = [],
                indexedOptions = _.indexBy(options, 'value');

            if (utils.isArray(value)) {
                formattedValue = value.map(function (value) {
                    return indexedOptions[value];
                });
            }

            return formattedValue;
        }
    });
});