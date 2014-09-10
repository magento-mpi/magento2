define([
    'ko',
    '../class',
    './initialize'
], function(ko, Class) {
    'use strict';

    function observe(obj, key, value){
        var method = Array.isArray(value) ? 'observableArray' : 'observable';

        obj[key] = ko[method](value);
    }

    return Class.extend({
        observe: function(path, value) {
            var key;

            if (typeof path === 'string') {
                observe(this, path, value);
            } else {
                for (key in path) {
                    observe(this, key, path[key]);
                }
            }
        }
    });
});