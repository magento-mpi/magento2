/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    '_',
    '../class',
    '../events'
], function(_, Class, EventsBus) {
    'use strict';

    return Class.extend({

        /**
         * Inits this.data to incoming data
         * @param  {Object} data
         */
        initialize: function(data) {
            this.data = data || {};
        },

        /**
         * If path specified, returnes this.data[path], else returns this.data
         * @param  {String} path
         * @return {*} this.data[path] or simply this.data
         */
        get: function(path) {
            return !path ? this.data : this.data[path];
        },

        /**
         * Sets value property to path and triggers update by path, passing result
         * @param {String|*} path
         * @param {Object} reference to instance
         */
        set: function(path, value){
            var result = this._override.apply(this, arguments);

            value   = result.value;
            path    = result.path;

            this.trigger('update', value);

            if (path) {
                this.trigger('update:' + path, value);
            }

            return this;
        },
        
        /**
         * Assignes props to this.data based on incoming params
         * @param  {String|*} path
         * @param  {*} value
         * @return {Object}
         */
        _override: function(path, value) {
            if (arguments.length > 1) {
                this.data[path] = value;
            } else {
                value = path;
                path = false;

                this.data = value;
            }

            return {
                path: path,
                value: value
            };
        }

    }, EventsBus);
});