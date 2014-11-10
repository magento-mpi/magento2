/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    './storages',
    'Magento_Ui/js/lib/registry/registry',
    'Magento_Ui/js/lib/class',
    'Magento_Ui/js/lib/events',
    'mage/utils'
], function(_, storages, registry, Class, EventsBus, utils){
    'use strict';
    
    var defaults = {
        stores: ['meta', 'data', 'params']
    };

    return Class.extend({
        /**
         * Initializes DataProvider instance.
         * @param {Object} settings - Settings to initialize object with.
         */
        initialize: function(settings) {
            _.extend(this, defaults, settings, settings.config || {});

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

                if(Array.isArray(config)){
                    config = {};
                }

                this[store] = new storage(config);
            }, this);

            return this;
        },

        /**
         * Assembles data and submits it using 'utils.submit' method
         */
        save: function(){
            var data = this.data.get();
            
            utils.submit({
                url: this.submit_url,
                data: data
            });
        }
    }, EventsBus);
});