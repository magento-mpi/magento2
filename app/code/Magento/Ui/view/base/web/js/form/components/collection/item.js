/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'Magento_Ui/js/form/component',
    'underscore',
    'mage/utils'
], function (Component, _, utils) {
    'use strict';

    function compact() {
        return _.compact.apply(_, arguments);
    };

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
        },

        initObservable: function () {
            var previewIndexes  = this.previewElements || [],
                previewCount    = previewIndexes.length;

            __super__.initObservable.apply(this, arguments);

            this.labelConfig        = this.label || {};
            this.previewIndexes     = previewIndexes;
            this._previewElements   = utils.reserve([], previewCount.length);

            this.observe('active')
                .observe('bodyElements',    [])
                .observe('headElements',    [])
                .observe('previewElements', [])
                .compute('label',           this.compositeLabel.bind(this));

            return this;
        },

        initElement: function (element) {
            var showAt  = element.displayArea || this.defaultDisplayArea,
                storage = this[showAt + 'Elements'];

            __super__.initElement.apply(this, arguments);

            element.setDataScope(this.dataScope + '.' + this.index);
            storage.push(element);
            this.addPreview(element);
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
                label       = compact(elements).map(getValues).join(separator).trim();
            }

            return label || defaultLabel;
        },

        getValues: function (separator, element) {
            var getValue = function (element) { return element.value() };

            return element.elems.map(getValue).join(separator);
        },

        addPreview: function (element) {
            var previewIndexes  = this.previewIndexes,
                previewElements = this._previewElements,
                position        = previewIndexes.indexOf(element.index);

            if (!!~position) {
                previewElements.splice(position, 1, element);
            }

            this.previewElements(compact(previewElements));
        }
    });
});