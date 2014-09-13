/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    '_',
    'Magento_Ui/js/lib/class',
    'Magento_Ui/js/lib/events'
], function(_, Class, EventsBus) {
    'use strict';

    return Class.extend({
        initialize: function(data) {
            this.data = data || {};
        },

        get: function(path) {
            return !path ? this.data : this.data[path];
        },

        _update: function(path, value) {
            var prop;

            if (arguments.length > 1) {
                prop = this.data[path] = this.data[path] || {};
            } else {
                value = path;
                path = false;

                prop = this.data;
            }

            _.extend(prop, value);

            return {
                path: path,
                value: value
            };
        },

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
        },

        set: function(extend, path, value){
            var args    = Array.prototype.slice.call(arguments),
                method  = '_override',
                result;

            if (typeof extend === 'boolean') {
                args.splice(0, 1);

                if (extend) {
                    method = '_update';
                }
            }

            result  = this[method].apply(this, args);
            value   = result.value;
            path    = result.path;

            this.trigger('update', value);

            if (path) {
                this.trigger('update:' + path, value);
            }

            return this;
        }

    }, EventsBus);
});