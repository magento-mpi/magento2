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
    
    return Class.extend({
        initialize: function(settings) {
            this.initStorages(settings)
                .initClient();
        },

        initStorages: function(settings) {
            var stores,
                storage;

            this.stores = ['config', 'meta', 'data', 'params', 'dump'];

            this.stores.forEach(function(store) {
                storage = storages[store];

                this[store] = new storage(settings[store]);
            }, this);

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

            return this;
        },

        onRead: function(result) {
            result = typeof result === 'string' ?
                JSON.parse(result) :
                result;

            result = {
                data: result.data
            };

            this.updateStorages(result)
                .trigger('refresh', result);
        }
    }, EventsBus);
});