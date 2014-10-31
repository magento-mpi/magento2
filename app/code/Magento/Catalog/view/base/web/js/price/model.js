/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'Magento_Catalog/js/price/component',
    'Magento_Ui/js/lib/class',
    'underscore'
],function (Component, Class, _) {
    'use strict';

    var PriceModel = Class.extend({
        initialize: function (config) {
            _.extend(this, config);

            this.pushConfig()
                .pushPriceData();
        },

        pushConfig: function () {
            var config = this.provider.config;

            _.each(this.config, function (value, key) {
                config.set(key, value);
            });

            return this;
        },

        pushPriceData: function () {
            var data = this.provider.data,
                amount,
                adjustments;

            _.each(this.prices, function (type, value) {
                amount      = value.amount;
                adjustments = value.adjustments;

                amount      && data.set('prices.'      + type, amount);
                adjustments && data.get('adjustments.' + type, adjustments);
            });

            return this;
        }
    });

    return Component(PriceModel);
});