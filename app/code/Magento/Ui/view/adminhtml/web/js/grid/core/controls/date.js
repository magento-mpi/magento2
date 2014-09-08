define([
    './abstract',
    '_'
], function (AbstractControl, _) {
    
    return AbstractControl.extend({
        initialize: function (data) {
            this.constructor.__super__.initialize.apply(this, arguments);
        }
    });
});