define(['ko', 'Magento_Ui/js/lib/class'], function(ko, Class) {

    return Class.extend({

        initialize: function(template) {
            this.templateName = template;
            this._data = {};
            this.nodes = ko.observable([]);
        },

        data: function(key, value) {
            if (arguments.length === 1) {
                return this._data[key];
            }

            this._data[key] = value;
        }
    });
});