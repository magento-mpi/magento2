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

    var PriceOptions = Class.extend({
        initialize: function (config) {
            _.extend(this, config);
        }
    });

    return Component(PriceOptions);
});