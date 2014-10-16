/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'Magento_Ui/js/initializer/collection',
    'Magento_Ui/js/form/component',
    'underscore'
], function (Collection, Component, _) {
    'use strict';

    var __super__ = Component.prototype;

    var FormCollection = Component.extend({
        initialize: function (config) {
            __super__.initialize.apply(this, arguments);
        },

        getTemplate: function () {
            return 'ui/form/components/collection/collection';
        }
    });

    return Collection(FormCollection);
});