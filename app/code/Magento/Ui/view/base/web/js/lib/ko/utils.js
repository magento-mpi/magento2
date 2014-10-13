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

    _.extend(ko.observableArray.fn, {
        contains: function (value) {
            return _.contains(this(), value);
        },

        hasNo: function (value) {
            return !this.contains.apply(this, arguments);
        }
    });
});