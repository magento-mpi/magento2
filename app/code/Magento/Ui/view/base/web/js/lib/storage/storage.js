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

        set: function(path, value){
            var result = this._override.apply(this, arguments);

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