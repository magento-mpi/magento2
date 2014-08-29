define([
    'ko',
    'Magento_Ui/js/framework/ko/scope',
    '_'
], function(ko, Scope, _) {

    return Scope.extend({
        initialize: function(initial, config, listing) {
            this.target = listing;

            _.extend( this, initial );

            this.observe({
                current: this.current,
                pageSize: this.pageSize
            });

            this.setParams();

            this.current.subscribe( this.reloadTarget.bind(this) );
            this.pageSize.subscribe( this.reloadTarget.bind(this) );
        },

        go: function( val ){
            var current = this.current;

            current( +current() + val );
        },

        next: function(){
            this.go( 1 );    
        },

        prev: function(){
            this.go( -1 );
        },

        isLast: function(){
            return +this.current() === this.total;
        },

        isFirst: function(){
            return +this.current() === 1;
        },

        setParams: function(){
            this.target.setParams({
                paging: {
                    pageSize: this.pageSize(),
                    current: this.current()
                }
            });
            
            return this;
        },

        reloadTarget: function(){
            this.setParams().target.reload();
        }
    });

});