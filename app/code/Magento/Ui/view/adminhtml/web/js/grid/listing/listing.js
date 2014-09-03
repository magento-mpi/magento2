define([
    '_',
    '../core/storage',
    'Magento_Ui/js/framework/ko/scope',
    'Magento_Ui/js/framework/mixin/loader'
], function(_, Storage, Scope, Loader) {
    'use strict';

    return Scope.extend({
        initialize: function(settings) {
            this.initObservable()
                .initStorage(settings)
                .updateItems();

            this.fields = this.storage.getMeta().fields;
        },

        initObservable: function(settings) {
            this.observe({
                rows: [],
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

        getSortableClassFor: function(heading) {
            var rule = heading.sorted;

            return rule ? 'sort-arrow-' + rule : 'not-sorted';
        },

        updateItems: function() {
            var items = this.storage.getData().items;

            this.rows(items);

            return this;
        },

        onLoad: function() {
            this.unlock()
                .updateItems();
        }
    }, Loader);
});