/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define(function (require) {
    'use strict';

    var Component   = require('Magento_Catalog/js/price/component'),
        Class       = require('Magento_Ui/js/lib/class'),
        Handlebars  = require('handlebars'),
        $           = require('jquery'),
        _           = require('underscore'),
        template    = require('text!Magento_Catalog/templates/price_view.html'),
        utils       = require('../price-utils');

    Handlebars = Handlebars || window.Handlebars;

    var formatPrice = function (format, amount, isShowSign) {
        return utils.formatPrice(amount, format, isShowSign);
    };

    var PriceView = Class.extend({

        initialize: function (config) {
            _.extend(this, defaults, config);

            this.element = $(this.element);

            this.initListeners()
                .process();
        },

        initListeners: function () {
            var provider    = this.provider,
                data        = provider.data,
                config      = provider.config,
                process     = this.process.bind(this),
                handlers    = { update: process };

            config
                .on(handlers);
            data
                .on(handlers);

            return this;
        },

        process: function () {
            var data = this.pull();

            this.render(data);

            return this;
        },

        pull: function () {
            var provider        = this.provider,
                config          = provider.config.get(),
                finalPrice      = this.getPrice('final'),
                oldPrice        = this.getPrice('old'),
                format          = formatPrice.bind(null, this.priceFormat),
                data;

            data = {
                finalPrice:      format(finalPrice.adjusted),
                finalPriceExcl:  format(finalPrice.raw),
                oldPrice:        format(oldPrice.adjusted),
                oldPriceExcl:    format(oldPrice.raw)
            };

            return _.extend(data, config);
        },

        getPrice: function (type) {
            var data            = this.provider.data,
                base            = data.get('prices.' + type),
                raw             = base,
                adjusted        = base,
                adjustments     = data.get('adjustments.' + type);

            adjusted = _.reduce(adjustments, function (summ, value) {
                return summ += value;
            }, adjusted);
            
            return {
                raw: raw,
                adjusted: adjusted
            };
        },

        _render: Handlebars.compile(template),

        render: function (data) {
            this.element.html(this._render(data));
        },
    });

    return Component(PriceView);
});