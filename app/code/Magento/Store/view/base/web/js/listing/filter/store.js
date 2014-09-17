/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'Magento_Ui/js/listing/filter/abstract',
    '_'
], function (AbstractControl, _) {
    'use strict';

    /**
     * Recursively loops through array of objects ({label: '...', value: '...'}
     *     or {label: '...', items: [...]}), looking for label, corresponding to value.
     * @param  {Array} arr
     * @param  {String} selected
     * @return {String} found label
     */
    function findIn(arr, selected) {
        var found;

        arr.some(function(obj){
            found = 'value' in obj ?
                obj.value == selected && obj.label :
                findIn(obj.items, selected);

            return found;
        });

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

            this.options = this.options || [];
        },

        /**
         * Checkes if current state is empty.
         * @return {Boolean}
         */
        isEmpty: function(){
            return !this.selected();
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

            console.log( selected );

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