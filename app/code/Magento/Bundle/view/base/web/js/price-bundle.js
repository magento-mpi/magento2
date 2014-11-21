/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'jquery',
    'underscore',
    'Magento_Catalog/js/price-utils'
], function ($,_, utils) {

    "use strict";

    var globalOptions = {
        productBundleSelector: '.product.bundle.option',
        qtyFieldSelector: '.qty'
    };

    $.widget('mage.priceBundle', {
        options: globalOptions,
        _init: initPriceBundle,
        _create: createPriceBundle,
        _setOptions: setOptions
    });

    return $.mage.priceBundle;

    function initPriceBundle() {
//        console.log(this);
    }

    function createPriceBundle() {
//        this._super();

        console.log('mage.priceBundle  ', this);
        var form = this.element;
        var bundleOptions = $(this.options.productBundleSelector, form);
        var qtyFields = $(this.options.qtyFieldSelector, form);

        bundleOptions.on('change', onBundleOptionChanged.bind(this));
        qtyFields.on('change', onQtyFieldChanged.bind(this));
//        form.on('changeOption', onFormChanged.bind(this));
    }

    function onBundleOptionChanged(event) {
        var changes;
        var bundleOption = $(event.target);
//        var handler = this.options.optionHandlers[option.data('role')];
//        option.data('optionContainer', option.closest(this.options.controlContainer));

//        if(handler && handler instanceof Function) {
//            changes = handler(option, this.options.optionConfig, this);
//        } else {
            changes = defaultGetOptionValue(bundleOption, this.options.optionConfig);
//        }

        $(this.element).trigger('changeOption', changes);
    }

    function defaultGetOptionValue(element, config) {
        var changes = {};
        var optionValue = element.val();
        var optionId = utils.findOptionId(element[0]);
        var optionName = element.prop('name');
        var optionType = element.prop('type');
        var optionConfig = config.options[optionId];

    }

    function onQtyFieldChanged(event) {
        console.log('onQtyFieldChanged ', event);
    }

    /**
     * Custom behavior on getting options:
     * now widget able to deep merge accepted configuration with instance options.
     * @param  {Object}  options
     * @return {$.Widget}
     */
    function setOptions(options) {
        $.extend(true, this.options, options);

        if('disabled' in options) {
            this._setOption('disabled', options['disabled']);
        }
        return this;
    }
});