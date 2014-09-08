define([
    'ko',
    '../class',
    '../initializers/ko'
], function(ko, Class) {

    return Class.extend({
        observe: function(path, value) {
            var key,
                value;

            if (typeof path === 'string') {
                this._observe(path, value);
            } else {
                for (key in path) {
                    this._observe(key, path[key]);
                }
            }
        },

        _observe: function(path, value) {
            var method = Array.isArray(value) ? 'observableArray' : 'observable';

            this[path] = ko[method](value);

            return this;
        }
    });
});