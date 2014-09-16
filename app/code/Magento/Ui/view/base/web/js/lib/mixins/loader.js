/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'Magento_Ui/js/lib/spinner'
], function (spinner) {
    'use strict';

    return {
        /**
         * Activates spinner
         * @return {Object} reference to instance
         */
        lock: function() {
            spinner.show();

            return this;
        },

        /**
         * Deactivates spinner
         * @return {Object} reference to instance
         */
        unlock: function() {
            spinner.hide();

            return this;
        }
    }
});