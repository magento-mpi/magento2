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

    var globalOptions = {
        productId: null,
        optionsSelector: '.product-custom-option'
    };

    $.widget('mage.priceOptions',{
        options: globalOptions,
        _create: initPriceOptions
    });

    return $.mage.priceOptions;

    function initPriceOptions() {

    }
});