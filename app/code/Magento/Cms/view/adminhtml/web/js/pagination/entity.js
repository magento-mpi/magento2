define([
    'Magento_Ui/js/framework/ko/scope',
    '_'
], function(Scope, _) {

    return Scope.extend({
        initialize: function(initial, config, listing) {
            this.target = listing;
            
            this.meta = this.target.meta;
            this.paging = this.target.paging;

            _.extend( this, initial );
        },

        go: function( val ){
            var current = this.paging.current;

            current( +current() + val );
        },

        next: function(){
            this.go( 1 );    
        },

        prev: function(){
            this.go( -1 );
        },

        isLast: function(){
            return +this.paging.current() === this.meta.pages();
        },

        isFirst: function(){
            return +this.paging.current() === 1;
        }
    });

});