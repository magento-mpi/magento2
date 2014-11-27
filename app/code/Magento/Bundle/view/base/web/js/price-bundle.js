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
        qtyFieldSelector: 'input.qty',
        priceBoxSelector: '.price-box',
        optionHandlers: {},
        controlContainer: 'dd' // should be eliminated
    };

    $.widget('mage.priceBundle', {
        options: globalOptions,
        _init: initPriceBundle,
        _create: createPriceBundle,
        updateProductSummary: updateProductSummary,
        _setOptions: setOptions
    });

    return $.mage.priceBundle;

    function initPriceBundle() {
        var form = this.element;
        var bundleOptions = $(this.options.productBundleSelector, form);

        if(this.options.optionConfig) {
            bundleOptions.trigger('change');
        }
    }

    function createPriceBundle() {
        var form = this.element;
        var bundleOptions = $(this.options.productBundleSelector, form);
        var qtyFields = $(this.options.qtyFieldSelector, form);

        bundleOptions.on('change', onBundleOptionChanged.bind(this));
        qtyFields.on('change', onQtyFieldChanged.bind(this));
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
        this.updateProductSummary();
    }

    function defaultGetOptionValue(element, config) {
        var changes = {};
        var optionValue = element.val() || null;
        var optionId = utils.findOptionId(element[0]);
        var optionName = element.prop('name');
        var optionType = element.prop('type');
        var optionConfig = config.options[optionId].selections;
        var optionHash;
        var optionQty  = 0;
        var tempChanges;
        var canQtyCustomize =false;
        var selectedIds = config.selected;

        switch (optionType) {
            case 'radio':
            case 'select-one':
                var qtyField = element.data('qtyField');
                qtyField.data('option', element);

                if (optionValue) {
                    optionQty = optionConfig[optionValue].qty || 0;
                    if(optionType === 'radio' && element.is(':checked') || optionType === 'select-one') {
                        canQtyCustomize = optionConfig[optionValue].customQty === '1';
                        toggleQtyField(qtyField, optionQty, optionId, optionValue, canQtyCustomize);
                        tempChanges = utils.deepClone(optionConfig[optionValue].prices);
                        tempChanges = applyTierPrice(tempChanges, optionQty, optionConfig[optionValue]);
                        tempChanges = applyQty(tempChanges, optionQty);
                    }
                } else {
                    toggleQtyField(qtyField, '', optionId, optionValue, false);
                }
                optionHash = 'bundle-option-' + optionName;
                changes[optionHash] = tempChanges || {};

                selectedIds[optionId] = [optionValue];
                break;
            case 'select-multiple':
                optionValue = _.compact(optionValue);
                _.each(optionConfig, function(row, optionValueCode) {
                    optionHash = 'bundle-option-' + optionName + '##' + optionValueCode;
                    optionQty = row.qty || 0;
                    tempChanges = utils.deepClone(row.prices);
                    tempChanges = applyTierPrice(tempChanges, optionQty, optionConfig);
                    tempChanges = applyQty(tempChanges, optionQty);
                    changes[optionHash] = _.contains(optionValue, optionValueCode) ? tempChanges : {};
                });

                selectedIds[optionId] = optionValue || [];
                break;
            case 'checkbox':
                optionHash = 'bundle-option-' + optionName + '##' + optionValue;
                optionQty = optionConfig[optionValue].qty || 0;
                tempChanges = utils.deepClone(optionConfig[optionValue].prices);
                tempChanges = applyTierPrice(tempChanges, optionQty, optionConfig);
                tempChanges = applyQty(tempChanges, optionQty);
                changes[optionHash] = element.is(':checked') ? tempChanges : {};

                selectedIds[optionId] = selectedIds[optionId] || [];
                if(!_.contains(selectedIds[optionId], optionValue) && element.is(':checked')) {
                    selectedIds[optionId].push(optionValue);
                } else if(!element.is(':checked')) {
                    selectedIds[optionId] = _.without(selectedIds[optionId], optionValue);
                }
                break;
        }

        return changes;
    }

    function onQtyFieldChanged(event) {
        var field = $(event.target);
        var optionInstance = field.data('option');
        var optionConfig = this.options.optionConfig.options[field.data('optionId')].selections[field.data('optionValueId')];
        optionConfig.qty = field.val();

        optionInstance.trigger('change');
    }

    function toggleQtyField(element, value, optionId, optionValueId, canEdit) {
        element
            .val(value)
            .data('optionId',optionId)
            .data('optionValueId',optionValueId)
            .attr('disabled', !canEdit);
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

    function applyTierPrice( oneItemPrice, qty, optionConfig ) {
        var tiers = optionConfig.tierPrice;
        var magicKey = _.keys(oneItemPrice)[0];
        _.each(tiers, function(tier) {
            if(tier.price_qty > qty) {
                return;
            }
            if(tier.prices[magicKey].amount < oneItemPrice[magicKey].amount) {
                oneItemPrice = tier.prices;
            }
        });
        return oneItemPrice;
    }

    function updateProductSummary() {
        this.element.trigger('updateProductSummary', {
            config: this.options.optionConfig
        });
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