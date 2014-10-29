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

    var globalOptions = {
        productId: null,
        prices: {},
        priceTemplate: '<span class="price">{{formatted}}</span>',
        boxTemplate: '{{price}}'
    };
    var hbs = Handlebars.compile;


    $.widget('mage.priceBox',{
        options: globalOptions,
        _create: initPriceBox
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

        var priceTemplate = this.priceTemplate = hbs(this.options.priceTemplate);
        var boxTemplate = this.boxTemplate = hbs(this.options.boxTemplate);
        var priceFormat = this.options.priceConfig.priceFormat;

        box.on('updatePrice', updatePrice.bind(this));
        box.on('reloadPrice', reloadPrice.bind(this));

        if(prices.special) {
            prices.final = prices.special;
        } else if(prices.regular) {
            prices.final = prices.regular;
        }


        _.each(prices, function(price, priceCode){
            var finalPrice = price.amount;
            _.each(price.adjustments, function(taxAmount){
                finalPrice += taxAmount;
            });
            var formatted = prices[priceCode]['formatted'] = utils.formatPrice(finalPrice, priceFormat);
            var html = priceTemplate({'formatted': formatted});
//            $('[data-product-id=' + productId + '] > [data-price-' + priceCode + ']').html(html);
//            $('[data-price-' + priceCode + ']', box).html(html);
        });
    }

    /**
     * Updates price via new (or additional values)
     * @param {Object} prices
     * @param {Boolean} toReplace
     */
    function updatePrice(prices, toReplace) {
        if(!!toReplace) {
            this.options.prices = prices;
        } else {
            _.extend(this.options.prices, prices);
        }
    }

    function reloadPrice() {

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
            console.log('Config:    ', config);
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