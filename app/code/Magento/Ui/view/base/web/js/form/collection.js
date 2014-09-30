/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'Magento_Ui/js/form/elements',
    'Magento_Ui/js/lib/ko/scope',
    'underscore',
    'jquery',
    'ko'
], function (controls, Scope, _, $, ko) {
    'use strict';

    var defaults = {
        meta: {
            title: '',
            template: 'ui/form/collection'
        }
    };

    return Scope.extend({

        initialize: function (config, refs) {
            $.extend(true, this, defaults, config, { refs: refs });

            this.initItems();
        },

        initItems: function () {
            this.items = this.value || [];
            delete this.value;

            this.observe('items', this.items.map(this.initItem, this));

            return this;
        },

        initItem: function (item, idx) {
            var elements = _.map(item.elements, this.initElement, this);

            return _.extend(item, { elements: elements });
        },

        initElement: function (value, name) {
            var meta    = this.meta[name],
                type    = meta.input_type,
                constr  = controls[type],
                element = new constr({
                    type: type,
                    meta: meta,
                    name: name,
                    value: value
                }, this.refs);

            return element;
        },

        getTemplate: function () {
            return this.meta.template;
        }
    });
});