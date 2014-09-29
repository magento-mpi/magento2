/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    './abstract',
    'underscore',
    'Magento_Ui/js/lib/i18n'
], function (AbstractElement, _, t) {
    'use strict';

    return AbstractElement.extend({

        /**
         * Invokes initialize method of parent class and initializes properties of instance.
         * @param {Object} config - form element configuration
         * @param {Number|String} value - initial value of form element
         */
        initialize: function (config, value) {
            this.constructor.__super__.initialize.apply(this, arguments);

            this.caption = this.caption || t('Select...');
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
        },

        /**
         * Stores element's value to registry by element's path value
         * @param  {String} path
         * @param  {Object} value - selected object
         */
        store: function (path, selected) {
            registry.set(selected.value, path);
        }
    });
});