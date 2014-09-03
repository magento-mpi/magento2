define([
    '_',
    'ko',
    'Magento_Ui/js/framework/ko/scope'
], function(_, ko, Scope) {
    'use strict';

    return Scope.extend({        
        initialize: function(config, initial) {
            this.storage = config.storage;
        
            this.meta = this.storage.getMeta();
            this.paging = this.storage.getParams().paging;

            this.sizes = config.sizes;

            this.initObservable()
                .initComputed();

            _.bindAll(this, 'reload');

            this.paging._current.subscribe( this.reload );
        },

        initObservable: function(){
            this.observe({
                'meta.pages': this.meta.pages,
                'meta.items': this.meta.items,
                'paging._current': this.paging.current,
                'paging.pageSize': this.paging.pageSize
            });

            return this;
        },

        initComputed: function(){
            this.paging.current = ko.pureComputed({
                read: function(){
                    return this.paging._current();
                },

                write: function( value ){
                    var valid;

                    valid = Math.min( Math.max(1, +value), this.meta.pages() );

                    return this.paging._current(valid);
                }
            }, this);

            return this;
        },

        go: function( val ){
            var current = this.paging.current;

            current( current() + val );
        },

        next: function(){
            this.go( 1 );    
        },

        prev: function(){
            this.go( -1 );
        },

        isLast: function(){
            return this.paging.current() === this.meta.pages();
        },

        isFirst: function(){
            return this.paging.current() === 1;
        },

        reload: function(){
            this.updateParams().storage.load();

            return this;
        },

        updateParams: function(){
            this.storage.setParams({
                paging: {
                    pageSize: this.paging.pageSize(),
                    current: this.paging.current()
                }
            });
            
            return this;
        }
    });
});