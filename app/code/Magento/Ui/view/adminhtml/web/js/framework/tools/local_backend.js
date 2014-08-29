define([
    '_',
    './fixtures'
], function (_, fixtures) {

    var LocalBackend = function (resource) {
        this.name = resource;
        this.storage = fixtures[this.name] || [];
    };

    _.extend(LocalBackend.prototype, {
        constructor: LocalBackend,

        searchBy: function (query, collection) {
            var type = typeof query,
                searchMethod = getSearchMethodFor(type),
                field, result;

            searchMethod = this[searchMethod] || noop;
            
            return _.filter(collection, function (entry) {
                for (field in entry) {
                    if (typeof entry[field] === type) {
                        result = searchMethod.call(this, query, field, entry);
                        if (result) {
                            return result;
                        }    
                    }
                }
            }, this);
        },

        searchByField: function (query, field, entry) {
            var type = typeof query;
            var searchMethod = getSearchMethodFor(type);

            searchMethod = this[searchMethod] || noop;

            return searchMethod.call(this, query, field, entry) || [];    
        },

        _stringSearchByField: function (strQuery, field, entry) {
            entry = entry[field].toLowerCase();

            return entry.indexOf(strQuery.toLowerCase()) !== -1;    
        },

        _booleanSearchByField: function (boolQuery, field, entry) {
            return entry[field] === boolQuery;
        },

        _numberSearchByField: function (numQuery, field, entry) {
            return entry[field] == numQuery;
        },

        readOne: function (id) {
            return findById(this.storage, id) || null;
        },

        readCollection: function (params) {
            var result  = this.storage,
                paging  = params.paging,
                filters = params.filters,
                query   = params.query;

            if (paging && paging.pageSize && paging.current) {
                result = this._getPageBy(paging.pageSize, paging.current);    
            }

            if (query) {
                result = this.searchBy(query, result);
            }

            return result;
        },

        _getPageBy: function (pageSize, targetPage) {
            var storage = this.storage,
                result  = [];

            var pagesNumber = parseInt(storage.length / pageSize, 10);

            if (storage.length % pageSize) {
                pagesNumber += 1;
            }

            var topMargin    = pageSize,
                bottomMargin = 0,
                pageCounter;

            for (pageCounter = 0; pageCounter < targetPage; pageCounter++) {
                result.push(storage.slice(bottomMargin, topMargin));

                bottomMargin += pageSize;
                topMargin    += pageSize;
            }

            return _.last(result);
        },

        create: function (entry) {
            this.storage.push(entry);
        },

        removeOne: function (id) {
            var position = indexOf(this.storage, id);
            var found = null;

            if (position >= 0) {
                removePositionIn(this.storage, position);
                found = id;
            }

            return found;
        },

        removeCollection: function (ids) {
            var removed = [];
            var position;

            if (ids) {
                _.each(ids, function (id) {
                    removed.push(this.removeOne(id));
                }, this);
            } else {
                removed = _.pluck(this.storage, 'id');
                this.storage = [];
            }
            
            return removed;
        }
    });

    return LocalBackend;

    function noop() {};

    function getSearchMethodFor(type) {
        return '_' + type + 'SearchByField';
    }

    function findById(collection, id) {
        return _.findOne(collection, { id: id });
    }

    function indexOf(collection, id) {
        return _.indexOf(collection, { id: id });
    }

    function removePositionIn(collection, position) {
        if (typeof collection === 'array') {
            collection.splice(position, 1);
        }
    }
});