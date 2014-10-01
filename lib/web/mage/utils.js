/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
//TODO: assemble all util methods in this module
define([], function () {
    'use strict';

    /**
     * Sets nested property of a specified object.
     * @param {Object} parent - Object to look inside for the properties.
     * @param {Array} parts - An array of properties names.
     * @param {*} value - Value of the last property in 'parts' array.
     * returns {*} New value for the property.
     */
    function setNested(parent, parts, value){
        var last = parts.pop();

        parts.forEach(function(part) {
            if (typeof parent[part] === 'undefined') {
                parent[part] = {};
            }

            parent = parent[part];
        });

        return (parent[last] = value);
    }


    /**
     * Retrieves value of a nested property.
     * @param {Object} parent - Object to look inside for the properties.
     * @param {Array} parts - An array of properties names
     * @returns {*} Value of the property.
     */
    function getNested(parent, parts){
        var exists;

        exists = parts.every(function(part) {
            parent = parent[part];

            return typeof parent !== 'undefined';
        });

        if(exists){
            return parent;
        }
    }

    return {

        /**
         * Generates a unique identifier.
         * @returns {String}
         * @private
         */
        uniqueid: function () {
            var idstr = String.fromCharCode((Math.random() * 25 + 65) | 0),
                ascicode;

            while (idstr.length < 5) {
                ascicode = Math.floor((Math.random() * 42) + 48);

                if (ascicode < 58 || ascicode > 64) {
                    idstr += String.fromCharCode(ascicode);
                }
            }

            return idstr;
        },

        /**
         * Retrieves or defines objects' property by a complex path.
         * @example
         *      byPath(obj, 'one.two.three', value)
         * @param {Object} ns - Container for the properties specified in path.
         * @param {String} path - Objects' properties divided by dots.
         * @param {*} [value] - New value for the last property.
         * @returns {*} Returns value of the last property in chain. 
         */
        nested: function(ns, path, value){
            path = path.split('.');

            return (
                arguments.length > 2 ? setNested : getNested
            )(ns, path, value);
        },

        /**
         * Defines if passed argument is array.
         * @param  {*}  obj
         * @return {Boolean}
         */
        isArray: function (obj) {
            return Object.prototype.toString.call(obj) === '[object Array]';
        }
    }
});