/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
/*global Handlebars*/
define([
    "jquery",
    "underscore",
    "handlebars",
    "jquery/ui"
], function($,_){
    "use strict";

    var globalOptions = {
        productId: null,
        priceHolderSelector: '.price-box',
        optionsSelector: '.product-custom-option',
        optionConfig: {}
    };

    $.widget('mage.priceOptions',{
        options: globalOptions,
        _create: initPriceOptions
    });

    return $.mage.priceOptions;

    function initPriceOptions() {
        console.log('Options:  ', this);
        console.log('Options:  ', this.options);

        var form = this.element;
        var options = $(this.options.optionsSelector, form);
        this.additionalPriceObject = {};

        options.on('change', onOptionChanged.bind(this));
    }

    function onOptionChanged(event) {
        console.log('OptionChanged: ', $(event.target).val(), findOptionId(event.target));

        var valueId = $(event.target).val();
        var optionId = findOptionId(event.target);
        var overhead = this.options.optionConfig[optionId][valueId];
        var additionalPrice = this.additionalPrice = {};

        this.additionalPriceObject[ optionId + '##' + valueId] = setOptionConfig(overhead);

        _.each(this.additionalPriceObject, function(prices){
            _.each(prices, function(priceValue, priceCode){
                additionalPrice[priceCode] = additionalPrice[priceCode] || {'amount':0, 'adjustments': {}};
                if(priceValue.amount)
                    additionalPrice[priceCode].amount = 0 + (additionalPrice[priceCode].amount || 0) + priceValue.amount;
                if(priceValue.adjustments)
                    _.each(priceValue.adjustments, function(adValue, adCode){
                        additionalPrice[priceCode].adjustments[adCode] = 0 + (additionalPrice[priceCode].adjustments[adCode] || 0) + adValue;
                    });
            });
        });

        $(this.options.priceHolderSelector).trigger('updatePrice', additionalPrice);

        console.log(this);
    }

    function findOptionId(element) {
        var name = $(element).attr('name');
        var re = new RegExp(/\[([^\]]+)?\]/);
        var id = re.exec(name)[1];

        if(id) {
            return id;
        } else {
            return undefined;
        }
    }

    function setOptionConfig (config) {
        if(!config) {
            config = {
                exclTaxPrice: 0,
                inclTaxPrice: 0
            };
        }
        var rightObj = {};
        rightObj.regular = {};
        rightObj.regular.amount = config.exclTaxPrice;
        rightObj.regular.adjustments = {};
        rightObj.regular.adjustments.tax = config.inclTaxPrice - config.exclTaxPrice;

        rightObj.special = rightObj.regular;

        return rightObj;
    }
});