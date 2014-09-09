define([
    './abstract',
    '_'
], function (AbstractControl, _) {
    'use strict';
    
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
        },

        reset: function () {
            this.to(null);
            this.from(null);

            return this.dump();
        }
    });
});