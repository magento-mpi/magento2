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
        prices: {},
        priceTemplate: '<span class="price">{{formatted}}</span>',
        boxTemplate: '{{price}}'
    };

    $.widget('mage.priceBox',{
        options: globalOptions,
        _create: initPriceBox
    });

    return $.mage.priceBox;

    function initPriceBox() {

    }
});