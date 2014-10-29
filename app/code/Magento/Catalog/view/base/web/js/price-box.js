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
    "Magento_Catalog/js/price-utils",
    "underscore",
    "handlebars",
    "jquery/ui"
], function($,utils, _){
    "use strict";

    var globalOptions = {
        productId: null,
        prices: {},
        priceTemplate: '<span class="price">{{formatted}}</span>',
        boxTemplate: '{{price}}'
    };
    var hbs = Handlebars.compile;


    $.widget('mage.priceBox',{
        options: globalOptions,
        _create: initPriceBox,
        updatePrice: updatePrice,
        reloadPrice: reloadPrice
    });

    return $.mage.priceBox;

    /**
     * Function-initializer of priceBox widget
     */
    function initPriceBox() {

        setDefaultsFromDataSet.call(this);
        setDefaultsFromPriceConfig.call(this);

        var box = this.element;
        var productId = this.options.productId;
        var prices = this.options.prices || {};
        var initial = this.initialPrices = utils.deepClone(this.options.prices);

        box.on('updatePrice', onUpdatePrice.bind(this)).trigger('updatePrice');
        box.on('reloadPrice', reloadPrice.bind(this)).trigger('reloadPrice');


    }

    /**
     * Call on event updatePrice. Proxy to updatePrice method.
     * @param {Event} event
     * @param {Object} prices
     * @param {Boolean} isReplace
     * @return {Function}
     */
    function onUpdatePrice (event, prices, isReplace) {
        return updatePrice.call(this, prices, isReplace);
    }

    /**
     * Updates price via new (or additional values)
     * @param {Object} newPrices
     * @param {Boolean} isReplace
     */
    function updatePrice(newPrices, isReplace) {
        var prices = this.options.prices;
        if(!!isReplace) {
            $.extend(true, prices, newPrices);
        } else {
            _.map(newPrices, function(priceValue, priceCode){
                var origin = this.initialPrices[priceCode];
                var option = newPrices[priceCode];
                var final = prices[priceCode];
                final.amount = 0 + origin.amount + (option.amount || 0);
                _.map(option.adjustments, function(pa, paCode){
                    final.adjustments[paCode] = 0 + origin.adjustments[paCode] + (option.adjustments[paCode] || 0);
                });
            }.bind(this));
        }

        this.element.trigger('reloadPrice');
    }

    function reloadPrice() {
        var prices = this.options.prices;
        var priceFormat = this.options.priceConfig.priceFormat;

        var priceTemplate = this.priceTemplate = hbs(this.options.priceTemplate);
        var boxTemplate = this.boxTemplate = hbs(this.options.boxTemplate);

        _.each(prices, function(price, priceCode){
            var html,
                finalPrice = price.amount;
            _.each(price.adjustments, function(adjustmentAmount){
                finalPrice += adjustmentAmount;
            });

            prices[priceCode]['final'] = finalPrice;
            prices[priceCode]['formatted'] = utils.formatPrice(finalPrice, priceFormat);
            html = priceTemplate(prices[priceCode]);

//            $('[data-product-id=' + productId + '] > [data-price-' + priceCode + ']').html(html);
//            $('[data-price-' + priceCode + ']', box).html(html);
            console.log('To render ', priceCode,': ', finalPrice, prices[priceCode]['formatted']);
        });

        if(prices.special) {
            prices.final = prices.special;
        } else if(prices.regular) {
            prices.final = prices.regular;
        }
    }



    // To remove after beckend done
    function setDefaultsFromDataSet () {
        var box = this.element;
        this.options.productId = box.data('productId');
    }

    function setDefaultsFromPriceConfig () {
        var config = this.options.priceConfig ;
        if(config) {
            if(+config.productId !== +this.options.productId) {
                return;
            }
            if(config.inclTaxPrice === config.productOldPrice) {
                this.options.prices['regular'] = {
                    'amount': config.productOldPrice * (1 - config.currentTax / 100),
                    'adjustments': {
                        'tax': config.productOldPrice * config.currentTax / 100
                    }
                };
            } else {
                this.options.prices['regular'] = {
                    'amount': config.productOldPrice * (1 - config.currentTax / 100),
                    'adjustments': {
                        'tax': config.productOldPrice * config.currentTax / 100
                    }
                };
                this.options.prices['special'] = {
                    'amount': config.exclTaxPrice,
                    'adjustments': {
                        'tax': config.exclTaxPrice * config.currentTax / 100
                    }
                };
            }
        }
    }
});