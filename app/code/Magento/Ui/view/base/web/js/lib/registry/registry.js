/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'mage/utils',
    './storage',
    './events'
], function(utils, Storage, Events) {
    'use strict';

    function Registry() {
        this.storage = new Storage();
        this.events = new Events(this.storage);
    }

    Registry.prototype = {
        constructor: Registry,

        /**
         * Retrieves data from registry.
         * @params {(String|Array)} elems -
                An array of elements' names or a string of names divided by spaces.
         * @params {Function} [callback] -
                Callback function that will be triggered
                when all of the elements are registered.
         * @returns {Array|*|Undefined}
                Returns either an array of elements
                or an element itself if only is requested.
                If callback function is specified then returns 'undefined'.
         */
        get: function(elems, callback) {
            var records;

            elems = utils.stringToArray(elems) || [];

            if (typeof callback !== 'undefined') {
                this.events.wait(elems, callback);
            } else {
                records = this.storage.get(elems);

                return elems.length === 1 ?
                    records[0] :
                    records;
            }
        },


       /**
         * Sets data to registry.
         * @params {(String|Array)} elems -
                An array of elements' names or a string of names divided by spaces.
         * @params {*} [value] -
                Value that will be assigned to elements.
         * @returns {registry} Chainable.  
         */
        set: function(elems, value) {
            elems = utils.stringToArray(elems);

            elems.forEach(function(elem){
                this.storage.set(elem, value);
                this.events.resolve(elem);
            }, this);

            return this;
        },

        /**
         * Removes specified elements from a storage.
         * @params {(String|Array)} elems -
                An array of elements' names or a string of names divided by spaces.
         * @returns {registry} Chainable.
         */
        remove: function(elems) {
            this.storage.remove(utils.stringToArray(elems));

            return this;
        },

       /**
         * Checks whether specified elements has been registered.
         * @params {(String|Array)} elems -
                An array of elements' names or a string of names divided by spaces.
         * @returns {Boolean}
         */
        has: function(elems) {
            return this.storage.has(utils.stringToArray(elems));
        },

        create: function(){
            return new Registry;  
        }
    };

    return new Registry;
});