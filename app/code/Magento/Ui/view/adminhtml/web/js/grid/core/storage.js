define([
    '_',
    './rest',
    'Magento_Ui/js/lib/ko/scope'
], function(_, Rest, Scope) {
    'use strict';

    return Scope.extend({
        initialize: function(config) {
            this.params = {};

            _.extend(this, config);

            this.initClient();
        },

        initClient: function() {
            var config;

            config = _.extend({
                onRead: this.onRead.bind(this)
            }, this.config.client);

            this.client = new Rest(config);

            return this;
        },

        load: function(options, callback) {
            var params;

            if (typeof options === 'function') {
                callback = options;
                options = {};
            }

            params = _.extend({}, this.params, options);

            if (this.beforeLoad) {
                this.beforeLoad();
            }

            this.client.read(params);

            return this;
        },

        getData: function() {
            return this.data;
        },

        setData: function(result) {
            _.extend(this.data, result);

            return this;
        },

        getParams: function() {
            return this.params;
        },

        setParams: function(params) {
            _.extend(this.params, params);

            return this;
        },

        getMeta: function() {
            return this.meta;
        },

        onRead: function(result) {
            this.setData(result)
                .trigger('load', result);
        }
    });
});