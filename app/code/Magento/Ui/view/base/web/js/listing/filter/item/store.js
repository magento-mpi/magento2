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

    function nestedObjectToArray(obj) {
        var target,
            items = [];

        for (var prop in obj) {

            target = obj[prop];
            if (typeof target.value === 'object') {

                target.items = nestedObjectToArray(target.value);
                delete target.value;
            }
            items.push(target);
        }

        return items;
    }

    return AbstractControl.extend({

        /**
         * Invokes initialize method of parent class and initializes properties of instance.
         * @param {Object} data - Item of "fields" array from grid configuration
         * @param {Object} config - Filter configuration
         */
        initialize: function (data) {
            this.constructor.__super__.initialize.apply(this, arguments);

            this.caption = 'Select...';

            this.observe('selected', '');

            this.options = this.options_tree ? this.formatOptions(this.options_tree) : [];
        },


        isEmpty: function(){
            var selected = this.selected();

            return !(selected && selected.value);
        },

        /**
         * Formats options property of instance.
         * @param {Object} options - object representing options
         * @returns {Array} - Options, converted to array
         */
        formatOptions: function (options) {
            return nestedObjectToArray(options);
        },

        display: function(){
            var selected = this.selected();

            return selected && selected.label;
        },

        /**
         * Returns dump of instance's current state
         * @returns {Object} - object which represents current state of instance
         */
        dump: function () {
            var selected = this.selected();

            this.output( this.display() );

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
        }
    });
});