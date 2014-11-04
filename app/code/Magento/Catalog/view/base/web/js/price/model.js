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
                adjustments,
                base,
                oldAdjustments;

            _.each(this.prices, function (value, type) {
                base        = value.base;
                adjustments = value.adjustments;

                !_.isUndefined(base) && data.set('prices.' + type, base);

                if (!_.isUndefined(adjustments) && _.isObject(adjustments)) {
                    oldAdjustments = data.get('adjustments.' + type) || {};

                    data.set('adjustments.' + type, _.extend(
                        {},
                        oldAdjustments,
                        adjustments
                    ));
                }
            });

            return this;
        }
    });

    return Component(PriceModel);
});