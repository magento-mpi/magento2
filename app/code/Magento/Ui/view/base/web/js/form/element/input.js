/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    './abstract'
], function (AbstractElement) {
    'use strict';

    return AbstractElement.extend({

        /**
         * Invokes initialize method of parent class.
         */
        initialize: function () {
            AbstractElement.prototype.initialize.apply(this, arguments);
        }
    });
});