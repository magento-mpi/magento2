define([
    '_',
    '../core/storage',
    'Magento_Ui/js/lib/ko/scope',
    'Magento_Ui/js/lib/mixin/loader'
], function(_, Storage, Scope, Loader) {
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
            var options;

            options = _.extend({
                beforeLoad: this.lock.bind(this)
            }, settings);

            this.storage = new Storage(options);

            this.storage.on('load', this.onLoad.bind(this));

            return this;
        },

        updateItems: function() {
            var items = this.storage.getData().items;

            this.rows(items);

            return this;
        },

        getCellTemplateFor: function (field) {
            if(field.template) {
                return field.template;
            } else {
                return this.getRootTemplatePath() +  '.cell.' + field.data_type;
            }
        },

        getTemplate: function () {
            return this.getRootTemplatePath();
        },

        getRootTemplatePath: function () {
            return 'Magento_Ui.templates.listing.' + this.view();
        },

        onLoad: function() {
            this.unlock()
                .updateItems();
        }
    }, Loader);
});