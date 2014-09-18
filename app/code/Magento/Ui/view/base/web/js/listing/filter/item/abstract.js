/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'Magento_Ui/js/lib/ko/scope',
    'underscore'
], function (Scope, _) {
    'use strict';
    
    return Scope.extend({

        /**
         * Extends instance with data passed.
         * @param {Object} data - Item of "fields" array from grid configuration
         * @param {Object} config - Filter configuration
         */
        initialize: function (data, config) {
            _.extend(this, data);
            this.config = config;

            this.observe('output', '');
        },

        isEmpty: function(){}
    });
});