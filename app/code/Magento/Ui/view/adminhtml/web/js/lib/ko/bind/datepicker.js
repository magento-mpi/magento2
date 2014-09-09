<!--
/**
 * {license_notice}
 *
 * @category    storage
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
-->
/** Creates datepicker binding and registers in to knockout.bindingHandlers object */
define([
    'ko',
    'jquery',
    'mage/calendar'
], function (ko, $) {
    'use strict';
    
    ko.bindingHandlers.datepicker = {
        /**
         * Initializes calendar widget on element and stores it's value to observable property.
         * @param {HTMLElement} el - Element, that binding is applied to
         * @param {Function} valueAccessor - Function that returns value, passed to binding
         */
        init: function (el, valueAccessor) {
            var config = valueAccessor(),
                observable,
                options = {};

            if (typeof config === 'object') {
                observable = config.storage;
                options    = config.options;
            } else {
                observable = config;
            }

            $(el).calendar(options);

            ko.utils.registerEventHandler(el, 'change', function (e) {
                var value = $(this).val();
                observable(value);
            });
        }
    }
});