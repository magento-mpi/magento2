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
                .observe('values');

            this.values.observe('value');

            return this;
        },

        initListeners: function () {
            this.active.subscribe(this.adjustListeners.bind(this));
        },

        adjustListeners: function (isActive) {
            isActive ? this.listen() : this.unbind();
        },

        listen: function () {
            var handlers = {
                'update': this.updateValue.bind(this)
            };

            this.elements.each(function (element) {
                element.on(handlers);
            });
        },

        unbind: function () {
            this.elements.each(function (element) {
                element.off('update');
            });
        },

        updateValue: function (element, settings) {
            var values          = this.values.indexBy('name'),
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

        apply: function () {
            var values = this.values.indexBy('name'),
                value;
                
            this.elements.each(function (element) {
                value = values[element.index];
                value = value && value.value();

                element.name = this.getElementName(element);
                element.set(value);
            }, this);
        },

        getElementName: function (element) {
            return [this.namespace, this.index, element.index].join('.');
        },

        getTemplate: function () {
            return 'ui/form/components/collection/item';
        }
    }, EventBus);
});