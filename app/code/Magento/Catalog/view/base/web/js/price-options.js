/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
define([
    "jquery",
    "underscore",
    "Magento_Catalog/js/price-utils",
    "jquery/ui"
], function($,_, utils){
    "use strict";

    var globalOptions = {
        productId: null,
        priceHolderSelector: '.price-box',
        optionsSelector: '.product-custom-option',
        optionConfig: {},
        optionHandlers: {},
        controlContainer: 'dd' // should be eliminated
    };

    $.widget('mage.priceOptions',{
        options: globalOptions,
        _create: initPriceOptions,
        _setOptions: setOptions
    });

    return $.mage.priceOptions;

    function initPriceOptions() {
        console.log('Options:  ', this);
        console.log('Options:  ', this.options);

        var form = this.element;
        var options = $(this.options.optionsSelector, form);
        this._additionalPriceObject = {};

        options.on('change', onOptionChanged.bind(this));
        form.on('changeOption', onFormChanged.bind(this));
    }

    function onOptionChanged(event) {
        var option = $(event.target);
        var optionType = option.prop('type');
        var changes;
        var handler = this.options.optionHandlers[option.data('role')];
        option.data('optionContainer', option.closest(this.options.controlContainer));

        if(handler && handler instanceof Function) {
            changes = handler(option, this.options.optionConfig, this);
        } else {
            changes = defaultGetOptionValue(option, this.options.optionConfig);
        }

        $(this.element).trigger('changeOption', changes);
    }

    function onFormChanged(event, priceChanges) {
        var additionalPrice = this.additionalPrice = {};
        if(priceChanges) {
            $.extend(this._additionalPriceObject, priceChanges);
        }

        _.each(this._additionalPriceObject, function(prices){
            _.each(prices, function(priceValue, priceCode){
                priceValue.amount = +priceValue.amount || 0;
                priceValue.adjustments = priceValue.adjustments || {};

                additionalPrice[priceCode] = additionalPrice[priceCode] || {'amount':0, 'adjustments': {}};
                additionalPrice[priceCode].amount = 0 + (additionalPrice[priceCode].amount || 0) + priceValue.amount;
                _.each(priceValue.adjustments, function(adValue, adCode){
                    additionalPrice[priceCode].adjustments[adCode] = 0 + (additionalPrice[priceCode].adjustments[adCode] || 0) + adValue;
                });
            });
        });

        $(this.options.priceHolderSelector).trigger('updatePrice', additionalPrice);
    }

    function defaultGetOptionValue(element, optionConfig) {
        var changes = {};
        var optionValue = element.val();
        var optionId = utils.findOptionId(event.target);
        var optionName = element.prop('name');
        var optionType = element.prop('type');
        var overhead;
        var optionHash;
        switch (optionType) {
            case 'text':
            case 'textarea':
                optionHash = optionName;
                overhead = optionValue ? optionConfig[optionId] : null;

                changes[ optionHash ] = utils.setOptionConfig(overhead);

                break;
            case 'radio':
            case 'select-one':
                optionHash = optionName;
                overhead = optionConfig[optionId][optionValue] || null;

                changes[ optionHash ] = utils.setOptionConfig(overhead);
                break;
            case 'select-multiple':
                _.each(optionConfig[optionId], function(prices, optionValueCode) {
                    optionHash = optionName + '##' + optionValueCode;
                    overhead = _.contains(optionValue, optionValueCode) ? prices : null;

                    changes[ optionHash ] = utils.setOptionConfig(overhead);
                });

                break;
            case 'checkbox':
                optionHash = optionName + '##' + optionValue;
                overhead = element.is(':checked') ? optionConfig[optionId][optionValue] : null;

                changes[ optionHash ] = utils.setOptionConfig(overhead);
                break;
            case 'file':
            case 'hidden':
            default:
                break;
        }

        return changes;
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