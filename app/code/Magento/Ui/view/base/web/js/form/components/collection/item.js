/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'Magento_Ui/js/form/component'
], function (Component) {
    'use strict';

    var defaults = {
        active: false,
        template: 'ui/form/components/collection/item'
    };

    var __super__ = Component.prototype;

    return Component.extend({
        initialize: function () {
            _.extend(this, defaults);

            __super__.initialize.apply(this, arguments);
        },

        initObservable: function () {
            __super__.initObservable.apply(this, arguments);

            this.observe('active');

            return this;
        }
    });
});