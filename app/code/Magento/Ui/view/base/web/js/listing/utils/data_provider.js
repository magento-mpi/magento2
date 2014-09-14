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

            this.initStorages()
                .initClient();
        },

        initStorages: function(settings) {
            var storage,
                config;

            this.stores.forEach(function(store) {
                storage = storages[store];
                config  = this[store];

                this[store] = new storage(config);
            }, this);

            return this;
        },

        initClient: function() {
            var config = this.config.get('client');

            this.client = new Rest(config);

            this.client.on('read', this.onRead.bind(this));

            return this;
        },

        refresh: function(options) {
            var stored = this.params.get(),
                params = _.extend({}, stored, options || {});

            this.trigger('beforeRefresh')
                .client.read(params);

            return this;
        },

        updateStorages: function(data) {
            var value;

            this.stores.forEach(function(store) {
                value = data[store];

                if(value){
                    this[store].set(value);
                }
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