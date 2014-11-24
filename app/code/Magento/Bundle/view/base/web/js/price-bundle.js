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
        this.element.trigger('updateProductSummary', {
            config: this.options.bundleConfig
        });
    }

    function onBundleOptionChanged(event) {
        var changes;
        var bundleOption = $(event.target);
        var priceBox = $(this.options.priceBoxSelector);
        var handler = this.options.optionHandlers[bundleOption.data('role')];
        bundleOption.data('optionContainer', bundleOption.closest(this.options.controlContainer));
        bundleOption.data('qtyField', bundleOption.data('optionContainer').find(this.options.qtyFieldSelector));

        if(handler && handler instanceof Function) {
            changes = handler(bundleOption, this.options.optionConfig, this);
        } else {
            changes = defaultGetOptionValue(bundleOption, this.options.optionConfig);
        }

        priceBox.trigger('updatePrice', changes);
        this.element.trigger('updateProductSummary', {
            config: this.options.bundleConfig
        });
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
                if(optionValue) {
                    optionQty = optionConfig[optionValue].qty || 0;
                    tempChanges = utils.deepClone(optionConfig[optionValue].prices);
                    tempChanges = applyTierPrice(tempChanges, optionQty, optionConfig);
                    tempChanges = applyQty(tempChanges, optionQty);
                }
                changes[optionHash] = tempChanges || {};
                break;
            case 'select-multiple':
                _.each(optionConfig, function(row, optionValueCode) {
                    optionHash = 'bundle-option-' + optionName + '##' + optionValueCode;
                    optionQty = row.qty || 0;
                    tempChanges = utils.deepClone(row.prices);
                    tempChanges = applyTierPrice(tempChanges, optionQty, optionConfig);
                    tempChanges = applyQty(tempChanges, optionQty);
                    changes[optionHash] = _.contains(optionValue, optionValueCode) ? tempChanges : {};
                });
                break;
            case 'checkbox':
                optionHash = 'bundle-option-' + optionName + '##' + optionValue;
                optionQty = optionConfig[optionValue].qty || 0;
                tempChanges = utils.deepClone(optionConfig[optionValue].prices);
                tempChanges = applyTierPrice(tempChanges, optionQty, optionConfig);
                tempChanges = applyQty(tempChanges, optionQty);
                changes[optionHash] = element.is(':checked') ? tempChanges : {};
                break;
        }
        console.log(changes);
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

    function applyQty( prices, qty) {
        _.each(prices, function(everyPrice){
            everyPrice.amount *= qty;
            _.each(everyPrice.adjustments, function(el, index){
                everyPrice.adjustments[index] *= qty;
            });
        });
        return prices;
    }

    function applyTierPrice( prices, qty, tiers ) {
        return prices;
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