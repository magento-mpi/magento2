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
        priceTemplate: '<span class="price">{{formatted}}</span>'
    };
    var hbs = Handlebars.compile;


    $.widget('mage.priceBox',{
        options: globalOptions,
        _create: createPriceBox,
        _setOptions: setOptions,
        updatePrice: updatePrices,
        reloadPrice: reDrawPrices,

        initialPrices: {}
    });

    return $.mage.priceBox;

    /**
     * Function-initializer of priceBox widget
     */
    function createPriceBox() {
        setDefaultsFromPriceConfig.call(this);
        setDefaultsFromDataSet.call(this);

        var box = this.element;
        this.initialPrices = utils.deepClone(this.options.prices);

        box.on('updatePrice', onUpdatePrice.bind(this));
        box.on('reloadPrice', reDrawPrices.bind(this));
    }

    /**
     * Call on event updatePrice. Proxy to updatePrice method.
     * @param {Event} event
     * @param {Object} prices
     * @param {Boolean} isReplace
     * @return {Function}
     */
    function onUpdatePrice (event, prices, isReplace) {
        return updatePrices.call(this, prices, isReplace);
    }

    /**
     * Updates price via new (or additional values)
     * @param {Object} newPrices
     * @param {Boolean} isReplace
     */
    function updatePrices(newPrices, isReplace) {
        var prices = this.options.prices;
        if(!!isReplace) {
            $.extend(true, prices, newPrices);
        } else if(_.isEmpty(newPrices)) {
            this.options.prices = this.initialPrices;
        } else {
            _.map(newPrices, function(option, priceCode){
                var origin = this.initialPrices[priceCode] || {};
                var final = prices[priceCode] || {};
                option.amount = option.amount || 0;
                origin.amount = origin.amount || 0;
                origin.adjustments = origin.adjustments || {};
                final.adjustments = final.adjustments || {};

                final.amount = 0 + origin.amount + option.amount;
                _.map(option.adjustments, function(pa, paCode){
                    final.adjustments[paCode] = 0 + (origin.adjustments[paCode] || 0) + pa;
                });
            }.bind(this));
        }

        this.element.trigger('reloadPrice');
    }

    /**
     * Render price unit block.
     */
    function reDrawPrices() {
        var box = this.element;
        var prices = this.options.prices;
        var priceFormat = this.options.priceConfig && this.options.priceConfig.priceFormat || {};
        var priceTemplate = hbs(this.options.priceTemplate);

        _.each(prices, function(price, priceCode){
            var html,
                finalPrice = price.amount;
            _.each(price.adjustments, function(adjustmentAmount){
                finalPrice += adjustmentAmount;
            });

            price['final'] = finalPrice;
            price['formatted'] = utils.formatPrice(finalPrice, priceFormat);

            html = priceTemplate(price);
            $('[data-price-type="' + priceCode + '"]', box).html(html);

            console.log('To render ', priceCode,': ', prices[priceCode]['formatted'], prices[priceCode]['final']);
        });

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


    function setDefaultsFromDataSet () {
        var box = this.element;
        var priceHolders = $('[data-price-type]', box);
        var prices = this.options.prices;
        this.options.productId = box.data('productId');
        if(_.isEmpty(prices)) {
            priceHolders.each(function(index, element){
                var type = $(element).data('priceType');
                var amount = $(element).data('priceAmount');

                prices[type] = {amount:amount};
            });
        }
    }

    function setDefaultsFromPriceConfig () {
        var config = this.options.priceConfig ;
        if(config) {
            if(+config.productId !== +this.options.productId) {
                return;
            }
            this.options.prices = config.prices;
        }
    }
});