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
            this._super();
        }
    });
});