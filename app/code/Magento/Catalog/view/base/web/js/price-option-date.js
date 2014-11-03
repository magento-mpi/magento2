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
        _create: initOptionDate
    });

    return $.mage.priceOptionDate;

    /**
     * Function-initializer of priceBox widget
     */
    function initOptionDate() {
        var field = this.element;
        var form = field.closest(this.options.fromSelector);
        var dropdowns = $(this.options.dropdownsSelector, field);
        var dateOptionId;
        var priceOptionHandler = {};

        if(dropdowns.length) {
            dateOptionId = this.options.dropdownsSelector + dropdowns.attr('name');
            priceOptionHandler['optionHandlers'] = {};
            priceOptionHandler['optionHandlers'][dateOptionId] = onCalendarDropdownCahnge(dropdowns);

            dropdowns.data('role', dateOptionId);

            form.priceOptions(priceOptionHandler);
        }
    }

    /**
     * Custom handler for Date-with-Dropdowns option type.
     * @param  {jQuery} siblings
     * @return {Object} { optionHash : optionAdditionalPrice }
     */
    function onCalendarDropdownCahnge (siblings) {
        return function(element, optionConfig, form) {
            var changes = {};
            var optionId = utils.findOptionId(event.target);
            var overhead = optionConfig[optionId];
            var isNeedToUpdate = true;


            siblings.each(function(index, el){
                isNeedToUpdate = isNeedToUpdate && !!$(el).val();
            });

            overhead = isNeedToUpdate ? overhead : null;
            changes[optionId] = utils.setOptionConfig(overhead);

            return changes;
        }
    }



});