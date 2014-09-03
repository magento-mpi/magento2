define(['ko', 'Magento_Ui/js/framework/class'], function(ko, Class) {

    var Source = Class.extend({

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

    return {
        create: function (template) {
            return new Source(template);
        }
    }
});