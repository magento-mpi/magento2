/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'Magento_Ui/js/form/element/index',
    'Magento_Ui/js/lib/ko/scope',
    'underscore'
], function (controls, Scope, _) {
    'use strict';

    var defaults = {
        title: '',
        template: 'ui/form/collection'
    };

    return Scope.extend({

        initialize: function (config) {
            _.extend(this, defaults, config);

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
            var config  = this[name],
                type    = config.input_type,
                constr  = controls[type];

            _.extend(config, {
                name: name,
                value: value,
                refs: this.refs,
                type: type
            });

            return new constr(config);
        },

        getTemplate: function () {
            return this.template;
        }
    });
});