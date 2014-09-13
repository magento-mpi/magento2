/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'ko',
    'moment',
    'jquery',
    'date-format-normalizer'
], function(ko, moment, $, convert) {
    'use strict';

    ko.bindingHandlers.date = {
        init: function(element, valueAccessor, allBindingsAccessor, viewModel) {
            var config = valueAccessor(),
                format = convert(config.format),
                date   = moment(config.value).format(format);

            $(element).text(date);
        }
    };
});