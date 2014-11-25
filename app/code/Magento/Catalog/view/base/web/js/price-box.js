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
], function ($, utils, _) {
    "use strict";

    var globalOptions = {
        productId: null,
        prices: {},
        priceTemplate: '<span class="price">{{formatted}}</span>'
    };
    var hbs = Handlebars.compile;


    $.widget('mage.priceBox', {
        options: globalOptions,
        _init: initPriceBox,
        _create: createPriceBox,
        _setOptions: setOptions,
        updatePrice: updatePrices,
        reloadPrice: reDrawPrices,

        cache: {}
    });

    return $.mage.priceBox;

    /**
     * Widget initialisation.
     * Every time when option changed prices also can be changed. So
     * changed options.prices -> changed cached prices -> recalculation -> redraw price box
     */
    function initPriceBox() {
        var box = this.element;
        box.trigger('updatePrice');
    }

    /**
     * Widget creating.
     */
    function createPriceBox() {
        setDefaultsFromPriceConfig.call(this);
        setDefaultsFromDataSet.call(this);

        var box = this.element;
        this.cache.displayPrices = _.clone(this.options.prices);

        box.on('reloadPrice', reDrawPrices.bind(this));
        box.on('updatePrice', onUpdatePrice.bind(this));
    }

    /**
     * Call on event updatePrice. Proxy to updatePrice method.
     * @param {Event} event
     * @param {Object} prices
     * @param {Boolean} isReplace
     * @return {Function}
     */
    function onUpdatePrice(event, prices, isReplace) {
        return updatePrices.call(this, prices, isReplace);
    }

    /**
     * Updates price via new (or additional values).
     * It expects object like this:
     * -----
     *   "option-hash":
     *      "price-code":
     *         "amount": 999.99999,
     *         ...
     * -----
     * Empty option-hash object or empty price-code object treats as zero amount.
     * @param {Object} newPrices
     */
    function updatePrices(newPrices) {
        var prices = this.cache.displayPrices;
        var additionalPrice = {};

        this.cache.additionalPriceObject = this.cache.additionalPriceObject || {};
        if (newPrices) {
            $.extend(this.cache.additionalPriceObject, newPrices);
        }

        _.each(this.cache.additionalPriceObject, function (additional) {
            var keys = [];
            if (!_.isEmpty(additional)) {
                keys = _.keys(additional);
            } else if (!_.isEmpty(additionalPrice)) {
                keys = _.keys(additionalPrice);
            } else if (!_.isEmpty(prices)) {
                keys = _.keys(prices);
            }
            _.each(keys, function (priceCode) {
                var priceValue = additional[priceCode] || {};
                priceValue.amount = +priceValue.amount || 0;
                priceValue.adjustments = priceValue.adjustments || {};

                additionalPrice[priceCode] = additionalPrice[priceCode] || {'amount': 0, 'adjustments': {}};
                additionalPrice[priceCode].amount = 0 + (additionalPrice[priceCode].amount || 0) + priceValue.amount;
                _.each(priceValue.adjustments, function (adValue, adCode) {
                    additionalPrice[priceCode].adjustments[adCode] = 0 + (additionalPrice[priceCode].adjustments[adCode] || 0) + adValue;
                });
            });
        });

        if (_.isEmpty(additionalPrice)) {
            this.cache.displayPrices = _.clone(this.options.prices);
        } else {
            _.each(additionalPrice, function (option, priceCode) {
                var origin = this.options.prices[priceCode] || {};
                var final = prices[priceCode] || {};
                option.amount = option.amount || 0;
                origin.amount = origin.amount || 0;
                origin.adjustments = origin.adjustments || {};
                final.adjustments = final.adjustments || {};

                final.amount = 0 + origin.amount + option.amount;
                _.each(option.adjustments, function (pa, paCode) {
                    final.adjustments[paCode] = 0 + (origin.adjustments[paCode] || 0) + pa;
                });
            }, this);
        }

        this.element.trigger('reloadPrice');
    }

    /**
     * Render price unit block.
     */
    function reDrawPrices() {
        var box = this.element;
        var prices = this.cache.displayPrices;
        var priceFormat = this.options.priceConfig && this.options.priceConfig.priceFormat || {};
        var priceTemplate = hbs(this.options.priceTemplate);

        _.each(prices, function (price, priceCode) {
            var html,
                finalPrice = price.amount;
            _.each(price.adjustments, function (adjustmentAmount) {
                finalPrice += adjustmentAmount;
            });

            price['final'] = finalPrice;
            price['formatted'] = utils.formatPrice(finalPrice, priceFormat);

            html = priceTemplate(price);
            $('[data-price-type="' + priceCode + '"]', box).html(html);

            console.log('To render ', priceCode, ': ', prices[priceCode]['formatted'], prices[priceCode]['final']);
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

        if ('disabled' in options) {
            this._setOption('disabled', options['disabled']);
        }
        return this;
    }


    function setDefaultsFromDataSet() {
        var box = this.element;
        var priceHolders = $('[data-price-type]', box);
        var prices = this.options.prices;
        this.options.productId = box.data('productId');
        if (_.isEmpty(prices)) {
            priceHolders.each(function (index, element) {
                var type = $(element).data('priceType');
                var amount = $(element).data('priceAmount');

                prices[type] = {amount: amount};
            });
        }
    }

    function setDefaultsFromPriceConfig() {
        var config = this.options.priceConfig;
        if (config) {
            if (+config.productId !== +this.options.productId) {
                return;
            }
            this.options.prices = config.prices;
        }
    }
});