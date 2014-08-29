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

            this._bind()._subscribe();
        },

        _bind: function () {
            _.bindAll(this, '_formatData', '_load');

            return this;
        },

        _subscribe: function () {
            this.query.subscribe(function (query) {
                this.rawQuery(query.value);
            }, this);

            this.rawQuery.subscribe(function (rawQuery) {
                this.client
                    .read({ query: rawQuery })
                    .then(this._formatData)
                    .done(this.suggestions.bind(this));

            }, this);
        },

        search: function () {
            var query = this.rawQuery();

            this.target
                .setParams({ query: query })
                .paging.current(1);

            this.target.reload();
        },

        _formatData: function (result) {
            result = _.map(result.rows, function (entry) {
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