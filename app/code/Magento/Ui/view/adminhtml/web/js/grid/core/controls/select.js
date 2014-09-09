define([
    './abstract',
    '_'
], function (AbstractControl, _) {
    'use strict';

    function toArrayIgnoringKeys (object) {
        return _.map(object, function (value) { return value; });
    };

    return AbstractControl.extend({
        initialize: function (data) {
            this.constructor.__super__.initialize.apply(this, arguments);

            this.observe('selected', '');

            this.options = this.options ? this.formatOptions() : [];
        },

        formatOptions: function () {
            return toArrayIgnoringKeys(this.options);
        },

        dump: function () {
            var selected = this.selected();

            return {
                field: this.index,
                value: selected && selected.value
            }
        },

        reset: function () {
            this.selected(null);

            return this.dump();
        }
    });
});