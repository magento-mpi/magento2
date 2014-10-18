/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'Magento_Ui/js/form/component',
    'underscore',
    'ko'
], function (Component, _, ko) {
    'use strict';

    var defaults = {
        active: false,
        index: 0,
        template: 'ui/form/components/collection/item'
    };

    var __super__ = Component.prototype;

    return Component.extend({
        initialize: function () {
            _.extend(this, defaults);

            __super__.initialize.apply(this, arguments);
        },

        initObservable: function () {
            __super__.initObservable.apply(this, arguments);

            this.observe('active index')
                .observe('values', []);

            this.values.observe('value');

            return this;
        },

        initListeners: function () {
            __super__.initListeners.apply(this, arguments);

            return this;
        },

        initElement: function (element) {
            __super__.initElement.apply(this, arguments);

            element.on('update', this.updateValues.bind(this));
        },

        apply: function () {
            this.elems.each(function (element) {
                element
                    .setPath(this.path)
                    .setData(this.values());

            }, this);

            return this;
        },

        updateValues: function (element, settings) {
            var shouldUpdate   = this.active(),
                indexed,        
                input,
                valueStorage;

            if (!shouldUpdate) {
                return;
            }

            indexed         = this.values.indexBy('index');
            input           = settings.element;
            valueStorage    = indexed[input.index];

            if (valueStorage) {
                valueStorage.value(settings.value);
            } else {
                this.values.push({
                    index: input.index,
                    value: ko.observable(settings.value)
                });
            }
        },

        setIndex: function (index) {
            this.index(index);

            return this;
        },

        setPath: function (path) {
            this.path = path;

            return this;
        },

        setData: function (data) {
            this.values(data);

            return this;
        }
    });
});