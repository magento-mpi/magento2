define([
    '_',
    'jquery',
    '../core/storage',
    'Magento_Ui/js/lib/ko/scope',
    'Magento_Ui/js/lib/mixin/loader'
], function(_, $, Storage, Scope, Loader) {
    'use strict';

    return Scope.extend({
        initialize: function(settings) {
            this.initObservable()
                .initStorage(settings)
                .updateItems();

            this.fields = this.storage.getMeta().fields;
        },

        initObservable: function() {
            this.observe({
                rows: [],
                view: 'grid',
                isLocked: false
            });

            return this;
        },

        initStorage: function(settings) {
            var config,
                client;

            config = settings.config;
            client = config.client = config.client || {};

            _.extend(config, {
                beforeLoad: this.lock.bind(this)
            });

            $.extend(true, client, {
                ajax: {
                    data: {
                        name: config.name,
                        component: 'listing',
                        form_key: FORM_KEY
                    }
                }
            });

            this.storage = new Storage(settings);

            this.storage.on('load', this.onLoad.bind(this));

            return this;
        },

        updateItems: function() {
            var items = this.storage.getData().items;

            this.rows(items);

            return this;
        },

        getCellTemplateFor: function(field) {
            return this.getRootTemplatePath() + '.cell.' + field.data_type;
        },

        getTemplate: function() {
            return this.getRootTemplatePath();
        },

        getRootTemplatePath: function() {
            return 'Magento_Ui.templates.listing.' + this.view();
        },

        onLoad: function() {
            this.unlock()
                .updateItems();
        }
    }, Loader);
});