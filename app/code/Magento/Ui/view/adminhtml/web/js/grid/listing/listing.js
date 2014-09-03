define([
    '_',
    '../core/storage',
    'Magento_Ui/js/framework/ko/scope',
    'Magento_Ui/js/framework/mixin/loader'
], function(_, Storage, Scope, Loader){
    'use strict';

    return Scope.extend({
        initialize: function( config, initial ){
            this.fields = config.fields;

            this.initObservable( initial )
                .initStorage( config, initial );
        },

        initObservable: function(initial){
            this.observe({
                rows:       initial.data,
                isLocked:   false
            });

            return this;
        },

        initStorage: function( config, initial ){
            var options;

            options = {
                namespace: config.namespace,
                beforeLoad: this.lock.bind(this)
            };

            _.extend( options, config.storage );

            this.storage = new Storage(options);

            this.storage
                .setResult( initial )
                .on( 'load', this.onReload.bind(this) );

            return this;
        },

        getSortableClassFor: function (heading) {
            var rule = heading.sorted;
            
            return rule ? 'sort-arrow-' + rule : 'not-sorted';
        },

        onReload: function( result ){
            this.unlock()
                .rows( result.data );
        }
    }, Loader); 
});
