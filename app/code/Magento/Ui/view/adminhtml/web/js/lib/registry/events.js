/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    './storage'
], function(storage) {
    'use strict';

    var id = 0,
        requests = {},
        map = {};

    /**
     * Clears all of the entries of a specified request.
     * @param {Number} id - Id of request.
     */
    function clear(id) {
        var ei,
            elems,
            index,
            handlers;

        elems = requests[id].deps;

        for (ei = elems.length; ei--;) {
            handlers = map[elems[ei]];

            index = handlers.indexOf(id);

            if (~index) {
                handlers.splice(index, 1);
            }
        }

        delete requests[id];
    }


    /**
     * Tries to resolve pending request.
     * @param {Number} id - Id of request.
     * @returns {Boolean} Whether specified request was successfully resolved.
     */
    function resolve(id) {
        var request = requests[id],
            elems = request.deps,
            callback = request.callback,
            isResolved;

        isResolved = storage.has(elems);

        if (isResolved) {
            callback.apply(window, storage.get(elems));
        }

        return isResolved;
    }

    return {
        /**
         * Tries to resolve dependencies affected by the scpecified element.
         * @param {String} elem - Elements' name.
         * @returns {events} Chainable.
         */
        resolve: function(elem) {
            var pending = map[elem];

            if (typeof pending !== 'undefined') {
                pending
                    .filter(resolve)
                    .forEach(clear);
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
            if (storage.has(elems)) {
                return callback.apply(window, storage.get(elems));
            }

            elems.forEach(function(elem) {
                (map[elem] = map[elem] || []).push(id);
            });

            requests[id++] = {
                callback: callback,
                deps: elems
            };

            return this;
        }
    };
});