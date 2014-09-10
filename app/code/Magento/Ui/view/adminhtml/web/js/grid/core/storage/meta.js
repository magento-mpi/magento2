define([
    '_',
    './storage'
], function(_, Storage) {
    'use strict';

    return Storage.extend({
        initialize: function(data) {
            this.data = data || {};

            this.applyDefaults();
        },

        applyDefaults: function() {
            var defaults    = this.data.defaults,
                fields      = this.data.fields,
                key;

            if( !defaults ){
                return this;
            }

            fields.forEach(function(field) {
                for (key in defaults) {
                    if (!field.hasOwnProperty(key)) {
                        field[key] = defaults[key];
                    }
                }
            });

            return this;
        }
    });
});