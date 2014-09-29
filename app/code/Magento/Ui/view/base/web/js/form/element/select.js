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
], function (AbstractElement, _, t) {
    'use strict';

    var defaults = {
        caption: t('Select...')
    };

    return AbstractElement.extend({

        /**
         * Invokes initialize method of parent class and initializes properties of instance.
         * @param {Object} config - form element configuration
         * @param {Number|String} value - initial value of form element
         */
        initialize: function (config, value) {
            _.extend(this, defaults);

            this.constructor.__super__.initialize.apply(this, arguments);

            this.options = this.options ? this.formatOptions() : [];
        },

        /**
         * Formats options property of instance.
         * @param {Object} options - object representing options
         * @returns {Array} - Options, converted to array
         */
        formatOptions: function () {
            return _.map(this.options, function (value, key) {
                return { value: key, label: value  };
            });
        }
    });
});