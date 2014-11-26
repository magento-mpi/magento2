/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    "jquery",
    "underscore",
    "Magento_Catalog/js/price-utils",
    "jquery/ui"
], function($,_, utils){
    "use strict";

    var globalOptions = {
        productId: null,
        priceHolderSelector: '.price-box', //data-role="priceBox"
        optionsSelector: '.product-custom-option',
        optionConfig: {},
        optionHandlers: {},
        controlContainer: 'dd'
    };

    $.widget('mage.priceOptions',{
        options: globalOptions,
        _create: createPriceOptions,
        _setOptions: setOptions
    });

    return $.mage.priceOptions;

    function createPriceOptions() {
        var form = this.element;
        var options = $(this.options.optionsSelector, form);

        options.on('change', onOptionChanged.bind(this));
    }

    function onOptionChanged(event) {
        var changes;
        var option = $(event.target);
        var handler = this.options.optionHandlers[option.data('role')];
        option.data('optionContainer', option.closest(this.options.controlContainer));

        if(handler && handler instanceof Function) {
            changes = handler(option, this.options.optionConfig, this);
        } else {
            changes = defaultGetOptionValue(option, this.options.optionConfig);
        }

        $(this.options.priceHolderSelector).trigger('updatePrice', changes);
    }

    function defaultGetOptionValue(element, optionsConfig) {
        var changes = {};
        var optionValue = element.val();
        var optionId = utils.findOptionId(element[0]);
        var optionName = element.prop('name');
        var optionType = element.prop('type');
        var optionConfig = optionsConfig[optionId];
        var optionHash;
        switch (optionType) {
            case 'text':
            case 'textarea':
                optionHash = 'price-option-' + optionName;
                changes[optionHash] = optionValue ? optionConfig.prices : {};
                break;
            case 'radio':
            case 'select-one':
                optionHash = 'price-option-' + optionName;
                changes[optionHash] = optionConfig[optionValue] && optionConfig[optionValue].prices || {};
                break;
            case 'select-multiple':
                _.each(optionConfig, function(row, optionValueCode) {
                    optionHash = 'price-option-' + optionName + '##' + optionValueCode;
                    changes[optionHash] = _.contains(optionValue, optionValueCode) ? row.prices : {};
                });
                break;
            case 'checkbox':
                optionHash = 'price-option-' + optionName + '##' + optionValue;
                changes[optionHash] = element.is(':checked') ? optionConfig[optionValue].prices : {};
                break;
            case 'file':
                optionHash = 'price-option-' + optionName;
                // Checking for 'disable' property equal to checking DOMNode with id*="change-"
                changes[optionHash] = optionValue || element.prop('disabled') ? optionConfig.prices : {};
                break;
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