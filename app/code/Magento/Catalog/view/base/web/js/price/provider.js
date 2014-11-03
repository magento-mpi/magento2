/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'Magento_Ui/js/lib/storage/index',
    'Magento_Ui/js/lib/class',
    'Magento_Ui/js/lib/registry/registry',
    'Magento_Catalog/js/price/component',
    'underscore'
], function (storages, Class, registry, Component, _) {
    'use strict';

    var stores = ['data', 'params', 'config'];

    var Provider = Class.extend({
        initialize: function (config) {
            _.extend(this, config);

            this.initStorages();
        },

        initStorages: function () {
            stores.forEach(function (store) {
                this[store] = new storages[store];
            }, this);
        }
    });

    return Component(Provider);
});