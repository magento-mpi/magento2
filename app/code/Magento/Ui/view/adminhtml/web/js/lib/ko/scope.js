define([
    '../class',
    '../events',
    '../utils',
    'ko',
    '../initializers/ko'
], function(Class, EventBus, utils, ko) {

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

            utils.setValueByPathIn(this, path, ko[method](value));

            return this;
        }
    }, EventBus);
});