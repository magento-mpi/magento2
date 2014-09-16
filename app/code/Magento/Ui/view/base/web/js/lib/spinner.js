/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'jquery'
], function ($) {

    var spinner = $('[data-role="spinner"]');

    return {
        show: function () {
            spinner.show();
        },

        hide: function () {
            spinner.hide();
        }
    }
});