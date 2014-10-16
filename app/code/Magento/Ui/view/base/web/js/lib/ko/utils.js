/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'ko',
    'underscore'
], function (ko, _) {

    function observe() {
        return ko.observable.apply(ko, arguments);
    };

    _.extend(ko.observableArray.fn, {
        contains: function (value) {
            return _.contains(this(), value);
        },

        hasNo: function (value) {
            return !this.contains.apply(this, arguments);
        },

        observe: function (keys) {
            var items = this(),
                value;

            keys = _.isArray(keys) ? keys : Array.prototype.slice.call(arguments);

            items.map(function (item) {

                keys.forEach(function (field) {
                    item[field] = observe(item[field]);    
                });

                return item;
            });
        },

        getLength: function () {
            return this().length;
        },

        indexBy: function (key) {
            return _.indexBy(this(), key);
        },

        each: function (iterator, ctx) {
            return _.each(this(), iterator, ctx);
        }
    });
});