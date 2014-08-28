define([
    'Magento_Ui/js/framework/ko/scope',
    'ko'
], function (Scope, ko) {

    return Scope.extend({
        _defQuery: { data: null, value: null },

        initialize: function (listing, config, initial) {

            this.target = listing;
            this.client = this.target.client;

            this.observe({
                query: this._defQuery,
                rawQuery: '',
                suggestions: []
            });

            this._bind();

            this.rawQuery.subscribe(function (rawQuery) {
                this.client
                    .read({ query: rawQuery })
                    // .then(this._load)
                    .then(this._formatData)
                    .done(this.suggestions.bind(this));

            }, this);
        },

        _bind: function () {
            _.bindAll(this, '_formatData', '_load');
        },

        _formatData: function (collection) {
            var result = _.map(collection, function (entry) {
                return {
                    value: entry.title,
                    data: entry
                }
            });
            
            return result;
        },

        _load: function (collection) {
            if (collection) {
                this.target.load(collection);    
            }

            return collection;
        }
    });
});