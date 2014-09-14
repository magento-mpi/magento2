/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    '_',
    '../lib/ko/scope',
    '../lib/component'
], function(_, Scope, Component) {
    'use strict';

    var defaults = {
        sizes: [5, 10, 20, 30, 50, 100, 200],
        params: {
            dir: 'paging',
            items: ['pageSize', 'current']
        }
    };

    var Paging = Scope.extend({
        initialize: function(config) {
            _.extend(this, defaults, config);

            this.initObservable(config)
                .initProvider()
                .pushParams();
        },

        initObservable: function(config) {
            var data = this.provider.data.get();

            this.observe({
                'pages':        data.pages || 1,
                'totalCount':   data.totalCount,
                'current':      this.current,
                'pageSize':     this.pageSize
            });

            return this;
        },

        initProvider: function(){
            var provider    = this.provider,
                params      = provider.params;

            _.bindAll(this, 'drop', 'onRefresh', 'pullParams');

            provider.on('refresh', this.onRefresh);

            params.on({
                'update:filter':    this.drop,
                'update:sorting':   this.drop,
                'update:paging':    this.pullParams
            });

            return this;
        },

        go: function(val) {
            var current = this.current;

            current(current() + val);

            this.reload();
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

        getInRange: function(page) {
            return Math.min(Math.max(1, page), this.pages());
        },
        
        drop: function() {
            this.current(1);

            this.pushParams();
        },

        onRefresh: function() {
            var data = this.provider.data.get();

            this.totalCount(data.totalCount);
            this.pages(data.pages || 1);
        },

        onSizeChange: function() {
            var size = this.pageSize();

            if (size * this.current() > this.totalCount()) {
                this.current(1);
            }

            this.reload();
        },

        onPageChange: function() {
            var current,
                valid;

            current = +this.current();
            valid = !isNaN(current) ? this.getInRange(current) : 1;

            this.current(valid);

            this.reload();
        }
    });

    return Component({
        constr: Paging
    });
});