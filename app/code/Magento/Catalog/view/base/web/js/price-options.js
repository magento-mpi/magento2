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
        console.log('OptionChanged: ', $(event.target).val(), findOptionId(event.target));

        var valueId = $(event.target).val();
        var optionId = findOptionId(event.target);
        var optionName = $(event.target).prop('name');
        var overhead = this.options.optionConfig[optionId][valueId];
        var changes = {};

        changes[ optionId + '##' + optionName + '##' + valueId] = setOptionConfig(overhead);

        $(this.element).trigger('changeOption', changes);
    }

    function onFormChanged(event, priceChanges) {
        var additionalPrice = this.additionalPrice = {};
        if(priceChanges) {
            $.extend(this._additionalPriceObject, priceChanges);
        }

        _.each(this._additionalPriceObject, function(prices){
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
    }

    /**
     * Custom behavior on getting options:
     * now widget able to deep merge of accepted configuration.
     * @param  {Object}  options
     * @return {mage.priceBox}
     */
    function setOptions(options) {
        $.extend(true, this.options, options);

        if('disabled' in options) {
            this._setOption('disabled', options['disabled']);
        }
        return this;
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

        if(config.oldPrice  !== config.price) {
            rightObj.regular = {amount: config.oldPrice};
        }

        return rightObj;
    }
});