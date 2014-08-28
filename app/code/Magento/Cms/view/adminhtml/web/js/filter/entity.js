define([
    'Magento_Ui/js/framework/ko/scope'
], function (Scope) {

    return Scope.extend({
        initialize: function (listing, config, initial) {
            this.target = listing;

            this.observe({
                query: this._defQuery,
                suggestions: []
            });

            this.query.subscribe(function (newQuery) {
                
            });
        },

        _defQuery: { data: null, value: null }
    });
});