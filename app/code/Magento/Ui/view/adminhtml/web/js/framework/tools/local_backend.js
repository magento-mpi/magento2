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
            var result = this.storage, paging, filters, query;

            if (params) {
                paging  = params.paging;
                filters = params.filters;
                query   = params.query;
            }

            if (query) {
                result = this.searchBy(query, result);
            }

            if (paging && paging.pageSize && paging.current) {
                result = this._getPageBy(paging.pageSize, paging.current, result);    
            }

            return result;
        },

        _getPageBy: function (pageSize, targetPage, collection) {
            var result  = [];

            var pagesNumber = parseInt(collection.length / pageSize, 10);

            if (collection.length % pageSize) {
                pagesNumber += 1;
            }

            var topMargin    = pageSize,
                bottomMargin = 0,
                pageCounter;

            for (pageCounter = 0; pageCounter < targetPage; pageCounter++) {
                result.push(collection.slice(bottomMargin, topMargin));

                bottomMargin += pageSize;
                topMargin    += pageSize;
            }

            return _.last(result);
        },

        create: function (entry) {
            this.storage.push(entry);
        },

        removeOne: function (id) {
            var found = findById(this.storage, id) || null;
            var position;

            if (found) {
                position = indexOf(this.storage, found);

                if (position >= 0) {
                    removePositionIn(this.storage, position);
                    found = id;
                }
            }

            return found;
        },

        removeCollection: function (ids) {
            var removed = [];

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
        return _.findWhere(collection, { id: id });
    }

    function indexOf(collection, item) {
        return _.indexOf(collection, item);
    }

    function removePositionIn(collection, position) {
        var removed = collection.splice(position, 1);
    }
});