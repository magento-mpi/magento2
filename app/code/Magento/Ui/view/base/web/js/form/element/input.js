/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    './abstract',
    'Magento_Ui/js/lib/component',
    'underscore'
], function (AbstractElement, Component, _) {
    'use strict';

    var InputElement = AbstractElement.extend({
        initialize: function (config, value) {
            this._super();

            this.observe('value', value);
        }
    });

    return Component({
        constr: InputElement
    });
});