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
            return !path ?
                this.data :
                (this.data[path] = this.data[path] || {});
        },

        set: function(path, value) {
            var prop,
                specify;

            if (typeof path === 'obj') {
                value = path;
                path = '';
            } else {
                specify = true;
            }

            prop = this.get(path);

            _.extend(prop, value);

            this.trigger('update', prop);

            if (specify) {
                this.trigger(path + 'Update', prop);
            }

            return this;
        }

    }, EventsBus);
});