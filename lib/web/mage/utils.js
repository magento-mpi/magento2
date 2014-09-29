/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
//TODO: assemble all util methods in this module
define([], function () {
    'use strict';

    function setByPath(parent, parts, value){
        var last = parts.pop();

        parts.forEach(function(part) {
            if (typeof parent[part] === 'undefined') {
                parent[part] = {};
            }

            parent = parent[part];
        });

        return (parent[last] = value);
    }

    function getByPath(parent, parts){
        var exists;

        exists = parts.every(function(part) {
            return typeof parent[part] !== 'undefined' ? 
                (parent = parent[part]) :
                false;
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
        byPath: function(ns, path, value){
            var parts = path.split('.'),
                action;

            action = arguments.length > 2 ?
                setByPath :
                getByPath;

            return action(ns, parts, value);
        }
    }
});