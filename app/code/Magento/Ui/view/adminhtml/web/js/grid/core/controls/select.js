define([
    './abstract',
    '_'
], function (AbstractControl, _) {

    return AbstractControl.extend({
        initialize: function (data) {
            this.constructor.__super__.initialize.apply(this, arguments);

            this.observe('selected', '');

            this.options = this.options || [];
            this.formatOptions();
        },

        formatOptions: function () {
            var id,
                newOption;

            this.options = this.options.map(function (option) {
                id = Object.keys(option)[0];
                newOption = { key: id, value: option[id] };

                return newOption;
            });
        }
    });
});