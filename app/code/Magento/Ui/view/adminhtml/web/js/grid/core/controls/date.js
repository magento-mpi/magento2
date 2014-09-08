define([
    './abstract',
    '_'
], function (AbstractControl, _) {
    
    return AbstractControl.extend({
        initialize: function (data) {
            this.constructor.__super__.initialize.apply(this, arguments);

            this.observe({
                from: '',
                to: ''
            });
        },

        dump: function () {
            return {
                field: this.index,
                value: {
                    from: this.from(),
                    to:   this.to()
                }
            }
        }
    });
});