/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'Magento_Ui/js/form/component',
    'underscore'
], function (Component, _) {
    'use strict';

    var __super__ = Component.prototype;

    var defaults = {
        lastIndex: 0,
        active: 0,
        template: 'ui/form/components/collection'
    };

    return Component.extend({
        initialize: function () {
            _.extend(this, defaults);

            __super__.initialize.apply(this, arguments);
        },

        initObservable: function () {
            __super__.initObservable.apply(this, arguments);

            this.observe('active');

            return this;
        },

        initElement: function (element) {
            __super__.initElement.apply(this, arguments);

            var index           = this.lastIndex++,
                data            = this.dataFor(index),
                path            = this.pathFor(index),
                shouldBeActive  = this.active() == index;

            element
                .setIndex(index)
                .setPath(path)
                .setData(data);

            if (shouldBeActive) {
                this._setActive(element);
            }
        },

        dataFor: function (index) {
            var indexed = this.getValues('by index'),
                value   = indexed[index] && indexed[index].value;

            value = value && _.map(value, function (value, index) {
                return {
                    index: index,
                    value: value
                }
            });

            return value;
        },

        pathFor: function (index) {
            return [this.namespace, index].join('.');
        },

        getValues: function (indexed) {
            var data    = this.provider.data,
                values  = data.get(this.namespace);

            values = _.map(values, function (value, index) {
                return {
                    value: value,
                    index: index
                };
            });

            return indexed ? _.indexBy(values, 'index') : values;
        },

        setActive: function (element) {
            return this._setActive.bind(this, element);
        },

        _setActive: function (element) {
            var index   = element.index(),
                inactive = this.elems.without(element);

            this.active(index);
            this.activate(element);
            this.deactivate(inactive);

            element.apply();
        },

        deactivate: function (elements) {
            elements.each(function (element) {
                element.active(false);
            });

            return this;
        },

        activate: function (element) {
            element.active(true);
        }
    });
});