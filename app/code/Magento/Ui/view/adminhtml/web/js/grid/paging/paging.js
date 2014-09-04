define([
    '_',
    'ko',
    'Magento_Ui/js/lib/ko/scope'
], function(_, ko, Scope) {
    'use strict';

    return Scope.extend({
        initialize: function(config) {
            var data;

            this.storage = config.storage;
            this.sizes = config.sizes;

            _.bindAll(this, 'reload', 'onLoad');

            this.initObservable(config)
                .initComputed()
                .updateParams();

            this.current.subscribe(this.reload);

            this.storage.on('load', this.onLoad);
        },

        initObservable: function(config) {
            var data = this.storage.getData();

            this.observe({
                'pages': data.pages,
                'totalCount': data.totalCount,
                '_current': config.params.current,
                'pageSize': config.params.pageSize
            });

            return this;
        },

        initComputed: function() {
            this.current = ko.pureComputed({
                read: function() {
                    return this._current();
                },

                write: function(value) {
                    var valid;

                    valid = Math.min(Math.max(1, +value), this.pages());

                    return this._current(valid);
                }
            }, this);

            return this;
        },

        go: function(val) {
            var current = this.current;

            current(current() + val);
        },

        next: function() {
            this.go(1);
        },

        prev: function() {
            this.go(-1);
        },

        isLast: function() {
            return this.current() === this.pages();
        },

        isFirst: function() {
            return this.current() === 1;
        },

        reload: function() {
            this.updateParams().storage.load();

            return this;
        },

        updateParams: function() {
            this.storage.setParams({
                paging: {
                    pageSize: this.pageSize(),
                    current: this.current()
                }
            });

            return this;
        },

        onLoad: function() {
            var data = this.storage.getData();

            this.totalCount(data.totalCount);
            this.pages(data.pages);
        }
    });
});