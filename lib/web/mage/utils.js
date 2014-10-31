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
        nested: function(data, path, value){
            var action = arguments.length > 2 ? setNested : getNested;

            path = path ? path.split('.') : [];

            return action(data, path, value);
        },

        reserve: function(container, size, offset){
            container.splice(offset || 0, 0, new Array(size));

            return _.flatten(container);
        },

        identical: function(){
            var arrays = _.toArray(arguments);

            return arrays.every(function(array) {
                return array.length == arrays[0].length && !_.difference(array, arrays[0]).length;
            });
        },

        stringToArray: function(str, separator){
            separator = separator || ' ';

            return typeof str === 'string' ?
                str.split(separator) :
                str;
        },

        flatten: function(data, separator, parent, result){
            separator   = separator || '.';
            result      = result || {};

            _.each(data, function(node, name){
                name = parent ?
                    (parent + separator + name) :
                    name;

                typeof node === 'object' ?
                    this.flatten(node, separator, name, result) :
                    (result[name] = node);

            }, this);

            return result;
        },

        unflatten: function(data, separator){
            var result = {};

            separator = separator || '.';

            _.each(data, function(value, nodes){
                nodes = nodes.split(separator);

                setNested(result, nodes, value);
            });

            return result;
        },

        serialize: function(data){
            var result = {},
                name;

            data = this.flatten(data);

            _.each(data, function(value, keys){
                name            = this.serializeName(keys);
                result[name]    = value;
            }, this);

            return result;
        },

        serializeName: function(name, separator){
            var result;

            separator   = separator || '.';
            name        = name.split(separator);

            result = name.shift();

            name.forEach(function(part){
                result += '[' + part + ']';
            });

            return result;
        },

        submit: function(options){
            var form = document.createElement('form'),
                data,
                field;

            form.setAttribute('action', options.url);
            form.setAttribute('method', 'post');

            data = this.serialize(options.data);

            _.each(data, function(value, name){
                field = document.createElement('input');

                field.setAttribute('name', name);
                field.setAttribute('type', 'hidden');
                
                field.value = value;

                form.appendChild(field);
            });

            document.body.appendChild(form);

            form.submit();
        }
    }
});