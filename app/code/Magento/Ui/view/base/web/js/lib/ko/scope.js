/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
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
        },

        pushParams: function(){
            var params      = this.params,
                provider    = this.provider.params,
                data        = {};

            params.items.forEach(function(name) {
                data[name] = this[name]();
            }, this);

            provider.set(params.dir, data);

            return this;
        },

        pullParams: function(){
            var params      = this.params,
                provider    = this.provider.params,
                data        = provider.get(params.dir);

            params.items.forEach(function(name) {
                this[name](data[name]);
            }, this);

            return this;
        },

        reload: function() {
            this.pushParams()
                .provider.refresh();
        }
    });
});