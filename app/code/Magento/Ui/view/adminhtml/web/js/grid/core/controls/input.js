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
    
    return AbstractControl.extend({

        /**
         * Invokes initialize method of parent class and initializes observable properties of instance.
         * @param {Object} data - Item of "fields" array from grid configuration
         * @param {Object} config - Filter configuration
         */
        initialize: function (data) {
            this.constructor.__super__.initialize.apply(this, arguments);

            this.observe('value', '');
        },

        isEmpty: function(){
            return !this.value();
        },

        display: function(){
            return this.value();
        },

        /**
         * Returns dump of instance's current state
         * @returns {Object} - object which represents current state of instance
         */
        dump: function () {
            this.output( this.display() );
            
            return {
                field: this.index,
                value: this.value()
            };
        },

        /**
         * Resets state properties of instance and calls dump method.
         * @returns {Object} - object which represents current state of instance
         */
        reset: function () {
            this.value(null);

            return this.dump();
        }
    });
});