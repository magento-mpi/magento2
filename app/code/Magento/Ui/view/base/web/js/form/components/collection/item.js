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
            this.groups.subscribe(this.initElementListeners, this);
        },

        initElementListeners: function () {
            var listened    = this.listened,
                update      = this.updateValue.bind(this),
                alreadyListened;

            this.groups.each(function (group) {
                alreadyListened = listened.contains(group);

                if (!alreadyListened) {
                    group.on('update', update);
                    listened.push(group);
                }
            });
        },

        updateValue: function (group, settings) {
            var shouldUpdate = this.active(),
                values,
                valueStorage,
                element;

            if (!shouldUpdate) {
                return;
            }

            values          = this.getIndexedValues();
            valueStorage    = values[element.index];
            element         = settings.element;

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

        apply: function (group) {
            var values = this.getIndexedValues(),
                value;

            if (group) {
                this._apply(group);
            } else {
                this.groups.each(this._apply, this);
            }
        },

        _apply: function (group) {
            group.elems.each(this.__apply, this);
        },

        __apply: function (element) {
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