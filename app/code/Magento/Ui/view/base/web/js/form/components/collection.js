/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'Magento_Ui/js/initializer/collection',
    'Magento_Ui/js/form/component',
    './collection/item',
    'underscore'
], function (Collection, Component, Item, _) {
    'use strict';

    var __super__ = Component.prototype;

    var defaults = {
        active: 0
    };

    var FormCollection = Component.extend({
        initialize: function () {
            _.extend(this, defaults);

            __super__.initialize.apply(this, arguments);

            this.initItems().apply();
        },

        initItems: function () {
            var data = this.provider.data.get(this.data_namespace),
                items,
                config,
                values;

            items = _.map(data, function (value, index) {
                values = _.map(value, function (value, name) {
                    return {
                        value:  value,
                        name:   name
                    };
                });

                config = {
                    index: index,
                    values: values
                };

                return this.createItem(config);
            }, this);

            return this;
        },

        createItem: function (config) {
            var active      = this.active(),
                count       = this.items.getLength(),
                settings,
                item;

            settings = {
                index:      count,
                elements:   this.elems,
                value:      [],
                namespace:  this.data_namespace
            };

            _.extend(settings, config);

            settings.active = (active == settings.index);

            item = new Item(settings);

            this.items.push(item);

            return item;
        },

        setActive: function (index) {
            return this._setActive.bind(this, index);
        },

        _setActive: function (index) {
            var isActive;

            this.active(index);

            this.items.each(function (item) {
                isActive = item.index === index;
                item.active(isActive);
            });

            this.apply();
        },

        initElement: function () {
            __super__.initElement.apply(this, arguments);

            this.apply();
        },

        apply: function () {
            var active = this.active(),
                items  = this.items.indexBy('index');

            items[active].apply();
        },

        initObservable: function () {
            __super__.initObservable.apply(this, arguments);

            this.observe('active')
                .observe('items', []);

            return this;
        },

        getTemplate: function () {
            return 'ui/form/components/collection';
        }
    });

    return Collection(FormCollection);
});