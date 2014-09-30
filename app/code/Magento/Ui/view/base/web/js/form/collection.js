/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'Magento_Ui/js/form/elements',
    'Magento_Ui/js/lib/ko/scope',
    'underscore'
], function (controls, Scope, _) {
    'use strict';

    var defaults = {
        title: ''
    }

    return Scope.extend({

        initialize: function (config, refs) {
            _.extend(this, defaults, config, { refs: refs });

            this.initItems();
        },

        initItems: function () {
            this.items = this.value || [];
            delete this.value;

            this.observe('items', this.items.map(this.initItem, this));
        },

        initItem: function (item, idx) {
            return _.map(item, this.initElement, this);
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
            return 'ui/form/collection';
        }
    });
});