define([
    'ko',
    'Magento_Ui/js/framework/ko/scope',
    '_'
], function(ko, Scope, _) {

    return Scope.extend({

        initialize: function(initial, config, listing) {
            _.extend( this, initial );

            this.def( 'current', this.current )
                .def( 'pageSize', this.pageSize );

            this.current.subscribe( this.onChange.bind(this) );
            this.pageSize.subscribe( this.onChange.bind(this) );

            this.listing = listing;
        },

        next: function(){
            var current = this.current;

            current( +current() + 1);     
        },

        prev: function(){
            var current = this.current;

            current( +current() - 1 );
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

        onChange: function(){
            this.listing.setParams({
                paging: {
                    pageSize: this.pageSize(),
                    current: this.current()
                }
            });

            console.log('change');
        }
    });

});