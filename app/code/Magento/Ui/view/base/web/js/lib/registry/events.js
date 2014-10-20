/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
], function(_) {
    'use strict';

    function Events(storage){
        this.id = 0,
        
        this.requests   = {};
        this.map        = {};
        this.storage    = storage;

        _.bindAll(this, '_resolve', '_clear');
    }

    Events.prototype = {
        constructor: Events,

        /**
         * Tries to resolve dependencies affected by the scpecified element.
         * @param {String} elem - Elements' name.
         * @returns {events} Chainable.
         */
        resolve: function(elem) {
            var pending = this.map[elem];

            if (typeof pending !== 'undefined') {
                pending
                    .filter(this._resolve)
                    .forEach(this._clear);
            }

            return this;
        },


        /**
         * Creates a new request for the specified set
                of elements in case some of them wasn't registered yeat.
                Otherwise triggers callback immediately.
         * @param {Array} elems - Requested elements.
         * @param {Function} callback -
                Callback that will be triggered as soon as
                all of the elements will be registered. 
         * @returns {events} Chainable.
         */
        wait: function(elems, callback) {
            var storage = this.storage,
                map     = this.map;

            if (storage.has(elems)) {
                return callback.apply(null, storage.get(elems));
            }

            elems.forEach(function(elem) {
                (map[elem] = map[elem] || []).push(this.id);
            }, this);

            this.requests[this.id++] = {
                callback:   callback,
                deps:       elems
            };

            return this;
        },

        /**
         * Tries to resolve pending request.
         * @private
         * @param {Number} id - Id of request.
         * @returns {Boolean} Whether specified request was successfully resolved.
         */
        _resolve: function(id) {
            var requests    = this.requests,
                request     = requests[id],
                elems       = request.deps,
                storage     = this.storage,
                isResolved;

            isResolved = storage.has(elems);

            if (isResolved) {
                request.callback.apply(null, storage.get(elems));
            }

            return isResolved;
        },

        /**
         * Clears all of the entries of a specified request.
         * @private
         * @param {Number} id - Id of request.
         */
        _clear: function(id) {
            var map         = this.map,
                requests    = this.requests,
                elems       = requests[id].deps,
                index,
                handlers;

            elems.forEach(function(elem){
                handlers = map[elem];

                index = handlers.indexOf(id);

                if (~index) {
                    handlers.splice(index, 1);
                }
            });

            delete requests[id];
        }
    };

    return Events;
});

