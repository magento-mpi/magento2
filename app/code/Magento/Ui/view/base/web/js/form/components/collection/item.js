/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'Magento_Ui/js/lib/ko/scope',
    'Magento_Ui/js/lib/events',
    'underscore',
    'ko'
], function (Scope, EventBus, _, ko) {
    'use strict';

    var defaults = {
        active: false
    };

    function observable() {
        return ko.observable.apply(ko, arguments);
    }

    return Scope.extend({
        initialize: function (config) {
            _.extend(this, defaults, config);

            this.initObservable()
                .initListeners();
        },

        initObservable: function () {
            this.observe('active')
                .observe('values')
                .observe('listened', []);

            this.values.observe('value');

            return this;
        },

        initListeners: function () {
            this.elements.subscribe(this.initElementListeners, this);
        },

        initElementListeners: function () {
            var listened    = this.listened,
                update      = this.updateValue.bind(this),
                alreadyListened;

            this.elements.each(function (element) {
                alreadyListened = listened.contains(element);

                if (!alreadyListened) {
                    element.on('update', update);
                    listened.push(element);
                }
            });
        },

        updateValue: function (element, settings) {
            var shouldUpdate = this.active(),
                values,
                valueStorage;

            if (!shouldUpdate) {
                return;
            }

            values          = this.getIndexedValues();
            valueStorage    = values[element.index];

            if (!valueStorage) {
                this.values.push({
                    value: observable(settings.value),
                    name:  element.index
                });
            } else {
                valueStorage.value(settings.value);
            }
        },

        getIndexedValues: function () {
            return this.values.indexBy('name');
        },

        apply: function (element) {
            var values = this.getIndexedValues(),
                value;

            if (element) {
                this._apply(element);
            } else {
                this.elements.each(this._apply, this);
            }
        },

        _apply: function (element) {
            var values = this.getIndexedValues(),
                value;

            value = values[element.index];
            value = value && value.value();

            element.name = this.getElementName(element);
            element.set(value);
        },

        getElementName: function (element) {
            return [this.namespace, this.index, element.index].join('.');
        },

        getTemplate: function () {
            return 'ui/form/components/collection/item';
        }
    }, EventBus);
});