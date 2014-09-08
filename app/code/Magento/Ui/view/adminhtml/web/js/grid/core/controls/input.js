define([
    './abstract',
    '_'
], function (AbstractControl, _) {
    
    return AbstractControl.extend({
        initialize: function (data) {
            this.constructor.__super__.initialize.apply(this, arguments);

            this.observe('value', '');
        },

        dump: function () {
            return {
                field: this.index,
                value: this.value()
            }
        },

        reset: function () {
            this.value(null);

            return this.dump();
        }
    });
});