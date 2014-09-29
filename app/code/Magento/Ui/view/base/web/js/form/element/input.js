/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    './abstract',
    'Magento_Ui/js/lib/component',
    'underscore',
], function (AbstractElement, Component, _) {
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