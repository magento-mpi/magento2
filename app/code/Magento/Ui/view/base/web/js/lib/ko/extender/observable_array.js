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
    'use strict';

    function observeKeys(keys, array) {
        array.each(function (item) {
            keys.forEach(function (key) {
                item[key] = !ko.isObservable(item[key]) ? ko.observable(item[key]) : item[key];
            });
        });
    }

    _.extend(ko.observableArray.fn, {
        contains: function (value) {
            return _.contains(this(), value);
        },

        hasNo: function (value) {
            return !this.contains.apply(this, arguments);
        },

        observe: function (keys) {
            keys = _.isArray(keys) ? keys : Array.prototype.slice.call(arguments);

            observeKeys(keys, this);
            this.subscribe(observeKeys.bind(this, keys));
        },

        getLength: function () {
            return this().length;
        },

        indexBy: function (key) {
            return _.indexBy(this(), key);
        },

        each: function (iterator, ctx) {
            return _.each(this(), iterator, ctx);
        },

        map: function (iterator, ctx) {
            return _.map(this(), iterator, ctx);
        },

        filter: function (iterator, ctx) {
            return _.filter(this(), iterator, ctx);
        },

        without: function () {
            var args = Array.prototype.slice.call(arguments);

            args.unshift(this());

            return _.without.apply(_, args);
        },

        first: function () {
            return _.first(this());
        },

        last: function () {
            return _.last(this());
        },

        every: function (predicate, ctx) {
            return _.every(this(), predicate, ctx);
        },

        groupBy: function (iteratee, ctx) {
            return _.groupBy(this(), iteratee, ctx);
        }
    });
});