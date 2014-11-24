/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'jquery',
    'underscore',
    'Magento_Catalog/js/price-utils'
], function ($,_, utils) {

    "use strict";

    var globalOptions = {
        productBundleSelector: '.product.bundle.option',
        qtyFieldSelector: '.qty',
        priceBoxSelector: '.price-box',
        optionHandlers: {},
        controlContainer: 'dd' // should be eliminated
    };

    $.widget('mage.priceBundle', {
        options: globalOptions,
        _create: createPriceBundle,
        _setOptions: setOptions
    });

    return $.mage.priceBundle;

    function createPriceBundle() {
        var form = this.element;
        var bundleOptions = $(this.options.productBundleSelector, form);
        var qtyFields = $(this.options.qtyFieldSelector, form);

        bundleOptions.on('change', onBundleOptionChanged.bind(this)).trigger('change');
        qtyFields.on('change', onQtyFieldChanged.bind(this));
    }

    function onBundleOptionChanged(event) {
        var changes;
        var bundleOption = $(event.target);
        var priceBox = $(this.options.priceBoxSelector);
        var handler = this.options.optionHandlers[bundleOption.data('role')];
        bundleOption.data('optionContainer', bundleOption.closest(this.options.controlContainer));

        if(handler && handler instanceof Function) {
            changes = handler(bundleOption, this.options.optionConfig, this);
        } else {
            changes = defaultGetOptionValue(bundleOption, this.options.optionConfig);
        }

        priceBox.trigger('updatePrice', changes);
    }

    function defaultGetOptionValue(element, config) {
        var changes = {};
        var optionValue = element.val();
        var optionId = utils.findOptionId(element[0]);
        var optionName = element.prop('name');
        var optionType = element.prop('type');
        var optionConfig = config.options[optionId].selections;
        var optionHash;
        var optionQty  = 0;
        var tempChanges;

        switch (optionType) {
            case 'radio':
            case 'select-one':
                optionHash = 'bundle-option-' + optionName;
                changes[optionHash] = optionConfig[optionValue] && optionConfig[optionValue].prices || {};
                break;
            case 'select-multiple':
                _.each(optionConfig, function(row, optionValueCode) {
                    optionHash = 'bundle-option-' + optionName + '##' + optionValueCode;
                    changes[optionHash] = _.contains(optionValue, optionValueCode) ? row.prices : {};
                });
                break;
            case 'checkbox':
                optionHash = 'bundle-option-' + optionName + '##' + optionValue;
                optionQty = optionConfig[optionValue].qty;
                tempChanges = { 'finalPrice': {'amount': optionConfig[optionValue].price}};
                _.each(tempChanges, function(everyPrice){
                    everyPrice.amount *= optionQty;
                    _.each(everyPrice.adjustments, function(el, index){
                        everyPrice.adjustments[index] *= optionQty
                    });
                });
                changes[optionHash] = element.is(':checked') ? tempChanges : {};

                break;
        }
        return changes;
    }

    function onQtyFieldChanged(event) { }

    function toggleQtyField(element, value, canEdit) {
        element.val(value).attr('disabled', !canEdit);
        if (canEdit) {
            element.removeClass('qty-disabled');
        } else {
            element.addClass('qty-disabled');
        }
    }

    /**
     * Custom behavior on getting options:
     * now widget able to deep merge accepted configuration with instance options.
     * @param  {Object}  options
     * @return {$.Widget}
     */
    function setOptions(options) {
        $.extend(true, this.options, options);

        if('disabled' in options) {
            this._setOption('disabled', options['disabled']);
        }
        return this;
    }
});