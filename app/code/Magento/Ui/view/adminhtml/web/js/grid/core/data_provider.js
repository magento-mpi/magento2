/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    '_',
    './rest',
    './storage/index',
    'Magento_Ui/js/lib/class',
    'Magento_Ui/js/lib/events'
], function(_, Rest, storages, Class, EventsBus) {
    'use strict';
    
    return Class.extend({
        initialize: function(settings) {
            this.initStorages(settings)
                .initClient();
        },

        initStorages: function(settings) {
            var stores,
                storage;

            stores = ['config', 'meta', 'data', 'params', 'dump'];

            stores.forEach(function(store) {
                storage = storages[store];

                this[store] = new storage(settings[store]);
            }, this);

            this.stores = stores;

            return this;
        },

        initClient: function() {
            var config;

            config = _.extend({
                onRead: this.onRead.bind(this),
            }, this.config.get('client'));

            this.client = new Rest(config);

            return this;
        },

        refresh: function(options, callback) {
            var params;

            if (typeof options === 'function') {
                callback = options;
                options = {};
            }

            params = _.extend({}, this.params.get(), options);

            this.trigger('beforeRefresh')
                .client.read(params);

            return this;
        },

        updateStorages: function(data) {
            this.stores.forEach(function(store) {
                this[store].set(true, data[store]);
            }, this);
        },

        onRead: function(result) {
            this.updateStorages(result)
                .trigger('refresh', result);
        }
    }, EventsBus);
});