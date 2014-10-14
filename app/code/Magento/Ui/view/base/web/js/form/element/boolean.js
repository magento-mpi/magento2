/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    './abstract'
], function (Abstract) {
    'use strict';

    return Abstract.extend({

        /**
         * Invokes initialize method of parent class.
         */
        initialize: function (config) {
            config.value = !(config.value === undefined);
            
            Abstract.prototype.initialize.apply(this, arguments);
        }
    });
});