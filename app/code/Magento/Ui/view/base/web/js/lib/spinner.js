/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'jquery'
], function ($) {
    'use strict';

    var selector    = '[data-role="spinner"]',
        spinner     = $(selector);

    return {
        show: function () {
            spinner.show();
        },

        hide: function () {
            spinner.hide();
        },

        get: function (id) {
            return $(selector + '[data-component="' + id + '"]');
        }
    }
});