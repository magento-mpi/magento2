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
        initialize: function (data, config) {
            this.constructor.__super__.initialize.apply(this, arguments);

            this.observe({
                from: '',
                to:   ''
            });
        },

        /**
         * Returns dump of instance's current state
         * @returns {Object} - object which represents current state of instance
         */
        dump: function () {
            return {
                field: this.index,
                value: {
                    from: this.from(),
                    to:   this.to()
                }
            }
        },

        /**
         * Resets state properties of instance and calls dump method.
         * @returns {Object} - object which represents current state of instance
         */
        reset: function () {
            this.to(null);
            this.from(null);

            return this.dump();
        }
    });
});