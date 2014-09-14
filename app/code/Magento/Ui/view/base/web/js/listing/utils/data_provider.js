/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    '_',
    './rest',
    'Magento_Ui/js/lib/storage/index',
    'Magento_Ui/js/lib/class',
    'Magento_Ui/js/lib/events'
], function(_, Rest, storages, Class, EventsBus) {
    'use strict';
    
    var defaults = {
        stores: ['config', 'meta', 'data', 'params', 'dump']
    };

    return Class.extend({
        initialize: function(settings) {
            _.extend(this, defaults, settings);

            this.initStorages(settings)
                .initClient();
        },

        initStorages: function(settings) {
            var stores,
                storage;

            this.stores.forEach(function(store) {
                storage = storages[store];

                this[store] = new storage(settings[store]);
            }, this);

            return this;
        },

        initClient: function() {
            var config = this.config.get('client'),
                client;

            client = this.client = new Rest(config);

            client.on('read', this.onRead.bind(this));

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

            return this;
        },

        onRead: function(result) {
            result = {
                data: result.data
            };

            this.updateStorages(result)
                .trigger('refresh', result);
        }
    }, EventsBus);
});