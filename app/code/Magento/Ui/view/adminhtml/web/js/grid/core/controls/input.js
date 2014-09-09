<!--
/**
 * {license_notice}
 *
 * @category    storage
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
-->
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

        /** Returns object which represents current state of instance */
        dump: function () {
            return {
                field: this.index,
                value: this.value()
            }
        },

        /** Resets state properties of instance and then returns call of dump method */
        reset: function () {
            this.value(null);

            return this.dump();
        }
    });
});