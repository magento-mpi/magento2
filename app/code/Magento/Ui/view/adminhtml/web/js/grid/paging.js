define([
    '_',
    'Magento_Ui/js/lib/ko/scope',
    './core/component'
], function(_, Scope, Component) {
    'use strict';

    var defaults = {
        sizes: [5, 10, 20, 30, 50, 100, 200]
    };

    var Paging = Scope.extend({
        initialize: function(config) {
            _.extend(this, defaults, config);

            this.initObservable(config)
                .updateParams();

            this.provider.on('refresh', this.onRefresh.bind(this) );
        },

        initObservable: function(config) {
            var data = this.provider.data.get();

            this.observe({
                'pages':        data.pages,
                'totalCount':   data.totalCount,
                'current':      this.current,
                'pageSize':     this.pageSize
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

        getInRange: function( page ){
            return Math.min(Math.max(1, page), this.pages());
        },

        reload: function() {
            this.updateParams()
                .provider.refresh();

            return this;
        },

        updateParams: function() {
            var params = this.provider.params;

            params.set('paging', {
                pageSize: this.pageSize(),
                current: this.current()
            });

            return this;
        },

        onRefresh: function() {
            var data = this.provider.data.get();

            this.totalCount(data.totalCount);
            this.pages(data.pages);
        },

        onSizeChange: function(){
            var size = this.pageSize();

            if( size * this.current() > this.totalCount() ){
                this.current(1);
            }

            this.reload();
        },

        onPageChange: function(){
            var current,
                valid;

            current = +this.current();
            valid   = !isNaN(current) ? this.getInRange(current) : 1;

            this.current(valid);

            this.reload();
        }
    });

    return Component({
        constr: Paging
    });
});