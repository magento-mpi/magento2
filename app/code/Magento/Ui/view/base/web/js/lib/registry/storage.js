/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([], function(){
    'use strict';
    
    var data = {};

    return {
        /**
         * Retrieves values of the specified elements.
         * @param {Array} elems - An array of elements.
         * @returns {Array} Array of values. 
         */
        get: function(elems) {
            var result = [],
                record;

            elems.forEach(function(elem) {
                record = data[elem];

                result.push(record ? record.value : undefined);
            });

            return result;
        },


        /**
         * Sets key -> value pair.
         * @param {String} elem - Elements' name.
         * @param {*} value - Value of the element.
         * returns {storage} Chainable.
         */
        set: function(elem, value) {
            var record = data[elem] = data[elem] || {};

            record.value = value;

            return this;
        },


        /**
         * Removes specified elements from storage.
         * @param {Array} elems - An array of elements to be removed.
         * returns {storage} Chainable.
         */
        remove: function(elems) {
            elems.forEach(function(elem) {
                delete data[elem];
            });

            return this;
        },


        /**
         * Checks whether all of the specified elements has been registered.
         * @param {Array} elems - An array of elements.
         * @returns {Boolean}
         */
        has: function(elems) {
            return elems.every(function(elem) {
                return typeof data[elem] !== 'undefined';
            });
        }
    };
});
