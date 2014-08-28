define([
    'ko',
    'Magento_Ui/js/framework/ko/scope',
    '_'
], function(ko, Scope, _) {

    return Scope.extend({
        initialize: function(initial, config, listing) {
            this.listing = listing;

            _.extend( this, initial );

            this.observe({
                current: this.current,
                pageSize: this.pageSize
            });

            this.setParams();

            this.current.subscribe( this.onChange.bind(this) );
            this.pageSize.subscribe( this.onChange.bind(this) );
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

        getViewTemplate: function () {
            return 'Magento_Ui.templates.pagination.toolbar';
        },

        setParams: function(){
            this.listing.setParams({
                paging: {
                    pageSize: this.pageSize(),
                    current: this.current()
                }
            });
            
            return this;
        },

        onChange: function(){
            this.setParams().listing.reload();
        },

        onReload: function(){
            console.log('hello');
        }
    });

});