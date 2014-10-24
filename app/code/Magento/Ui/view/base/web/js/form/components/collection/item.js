/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'Magento_Ui/js/form/component',
    'ko',
    'underscore'
], function (Component, ko, _) {
    'use strict';

    var defaults = {
        active: false,
        template: 'ui/form/components/collection/item',
        defaultDisplayArea: 'body',
        defaultLabel: '',
        separator: ' '
    };

    var __super__ = Component.prototype;

    return Component.extend({
        initialize: function () {
            _.extend(this, defaults);

            __super__.initialize.apply(this, arguments);

            this.initTitle();
        },

        initObservable: function () {
            __super__.initObservable.apply(this, arguments);

            this.observe('active')
                .observe('bodyElements', [])
                .observe('headElements', [])
                .observe('displayed', []);

            return this;
        },

        initElement: function (element) {
            var showAt  = element.displayArea || this.defaultDisplayArea,
                storage = this[showAt + 'Elements'];

            __super__.initElement.apply(this, arguments);

            storage.push(element);
            this.updateDisplayed(element);
        },

        initTitle: function () {
            this.labelConfig = this.label || {};
            this.label       = ko.computed(this.compositeLabel.bind(this));

            return this;
        },

        compositeLabel: function () {
            var config          = this.labelConfig,
                defaultLabel    = config['default'] || this.defaultLabel,
                separator       = this.separator,
                parts           = config.compositeOf,
                indexed         = this.elems.indexBy('index'),
                getValues       = this.getValues.bind(this, separator),
                label           = '',
                elements;

            if (parts) {
                elements    = parts.map(function (part) { return indexed[part] });
                label       = _.compact(elements).map(getValues).join(separator).trim();
            }

            return label || defaultLabel;
        },

        getValues: function (separator, element) {
            var getValue = function (element) { return element.value() };

            return element.elems.map(getValue).join(separator);
        },

        updateDisplayed: function (element) {
            var config              = this.previewElements || [],
                shouldBeDisplayed   = !!~config.indexOf(element.index);

            if (shouldBeDisplayed) {
                this.displayed.push(element);
            }
        }
    });
});