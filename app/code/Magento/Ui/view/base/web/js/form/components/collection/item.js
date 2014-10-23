/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'Magento_Ui/js/form/component',
    'ko'
], function (Component, ko) {
    'use strict';

    var defaults = {
        active: false,
        template: 'ui/form/components/collection/item',
        defaultDisplayArea: 'body',
        defaultTitle: '',
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
                .observe('headElements', []);

            return this;
        },

        initElement: function (element) {
            var showAt  = element.displayArea || this.defaultDisplayArea,
                storage = this[showAt + 'Elements'];

            __super__.initElement.apply(this, arguments);

            storage.push(element);
        },

        initTitle: function () {
            this.titleConfig = this.title || {};
            this.title       = ko.computed(this.compositeTitle.bind(this));
        },

        compositeTitle: function () {
            var config          = this.titleConfig,
                defaultTitle    = config['default'] || this.defaultTitle,
                separator       = config.separator  || this.separator,
                values          = [],
                parts           = config.composite_of,
                indexed         = this.elems.indexBy('index'),
                title           = '';

            if (parts) {
                parts  = parts.map(function (index) { return indexed[index] });
                values = parts.map(this.getValues.bind(this, separator));
                title  = values.join(separator);
            }
            console.log(title);
            return title.length ? title : defaultTitle;
        },

        getValues: function (separator, element) {
            return element.elems
                .filter(function (element) { return element.hasChanged() })
                .map   (function (element) { return element.value() });
        }
    });
});