/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    '../storage/index',
    './component'
    'Magento_Ui/js/lib/class',
    'Magento_Ui/js/lib/events',
], function(_, Component, storages, Class, EventsBus)[
    'use strict';
    
    var defaults = {
        stores: ['meta', 'data', 'params', 'dump']
    };

    var Provider = Class.extend({
        /**
         * Initializes DataProvider instance.
         * @param {Object} settings - Settings to initialize object with.
         */
        initialize: function(settings) {
            _.extend(this, defaults, settings);

            this.initStorages();
        },

        /**
         * Creates instances of storage objects.
         * @returns {DataProvider} Chainable.
         */
        initStorages: function() {
            var storage,
                config;

            this.stores.forEach(function(store) {
                storage = storages[store];
                config  = this[store] || {};

                this[store] = new storage(config);
            }, this);

            return this;
        }
    });

    return Component({
        constr: Provider
    });
]);