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
     * Recursively loops over object's properties and converts it to array ignoring keys.
     * If type of 'value' properties is 'object', replaces it with 'items' property and
     *     invokes nestedObjectToArray on 'value'.
     * If type of 'value' keys is not 'object', is simply writes an object itself to result array. 
     * @param  {Object} obj
     * @return {Array} result array
     */
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

    /**
     * Recursively loops through array of objects ({label: '...', value: '...'}
     *     or {label: '...', items: [...]}), looking for label, corresponding to value.
     * @param  {Array} arr
     * @param  {String} selected
     * @return {String} found label
     */
    function findIn(arr, selected) {
        var found,
            obj,
            i;

        for (i = 0; i < arr.length; i++) {
            obj = arr[i];

            if ('value' in obj) {
                found = obj.value === selected && obj.label;
            } else {
                found = findIn(obj.items, selected);
            }

            if (found) {
                break;
            }
        }

        return found;
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
            return !this.selected();
        },

        /**
         * Formats options property of instance.
         * @param {Object} options - object representing options
         * @returns {Array} - Options, converted to array
         */
        formatOptions: function (options) {
            return nestedObjectToArray(options);
        },

        /**
         * Looks up through the options for label, corresponding to passed value
         * @param  {String} selected
         * @return {String} label
         */
        getLabelFor: function (selected) {
            var label = findIn(this.options, selected);

            return label;
        },

        /**
         * Returns dump of instance's current state
         * @returns {Object} - object which represents current state of instance
         */
        dump: function () {
            var selected = this.selected();
            this.output(this.getLabelFor(selected));

            return {
                field: this.index,
                value: selected
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