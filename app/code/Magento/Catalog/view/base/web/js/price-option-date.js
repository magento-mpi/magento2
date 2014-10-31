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
    "jquery/ui"
], function($,utils, _){
    "use strict";

    var globalOptions = {
        fromSelector: 'form',
        dropdownsSelector: '[data-role=calendar-dropdown]'
    };


    $.widget('mage.priceOptionDate',{
        options: globalOptions,
        _create: initPriceBox
    });

    return $.mage.priceBox;

    /**
     * Function-initializer of priceBox widget
     */
    function initPriceBox() {
        var field = this.element;
        var form = field.closest(this.options.fromSelector);
        var dropdowns = $(this.options.dropdownsSelector, field);

        form.priceOptions({'optionHandlers':{'calendar-dropdown': function(){
            console.log(arguments);
        }}});
    }

});