/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'ko',
    'moment',
    'jquery'
], function(ko, moment, $) {
    'use strict';

    ko.bindingHandlers.date = {
        init: function(element, valueAccessor, allBindingsAccessor, viewModel) {
            var config = valueAccessor(),
                format = config.format,
                date   = moment(config.value).format(format);

            $(element).text(date);
        }
    };
});