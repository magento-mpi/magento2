/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    './abstract',
    '_'
], function (AbstractControl, _) {
    'use strict';

    /**
     * Convertes object to array, ignoring it's keys.
     * @param {Object} object - Object to convert
     * @returns {Array} - Converted result
     */
    function toArrayIgnoringKeys(object) {
        return _.map(object, function (value) { return value; });
    };

    return AbstractControl.extend({

        /**
         * Invokes initialize method of parent class and initializes properties of instance.
         * @param {Object} data - Item of "fields" array from grid configuration
         * @param {Object} config - Filter configuration
         */
        initialize: function (data) {
            this.constructor.__super__.initialize.apply(this, arguments);

            this.observe('selected', '');

            this.options = this.options ? this.formatOptions(this.options) : [];
        },

        /**
         * Formats options property of instance using toArrayIgnoringKeys function.
         * @param {Object} options - object representing options
         * @returns {Array} - Options, converted to array
         */
        formatOptions: function (options) {
            return toArrayIgnoringKeys(options);
        },

        /**
         * Returns dump of instance's current state
         * @returns {Object} - object which represents current state of instance
         */
        dump: function () {
            var selected = this.selected();

            return {
                field: this.index,
                value: selected && selected.value
            }
        },

        /**
         * Resets state properties of instance and calls dump method.
         * @returns {Object} - object which represents current state of instance
         */
        reset: function () {
            this.selected(null);

            return this.dump();
        }
    });
});