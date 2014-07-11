/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
(function (factory) {
    if (typeof define !== 'undefined' && define.amd) {
        define([
            'jquery',
            'mage/backend/validation'
        ], factory);
    } else {
        factory(window.jQuery);
    }
})(function ($) {
    "use strict";
    rma.addLoadProductsCallback(function () {
        $('[class^="rma-action-links-"]').each(function (el, val) {
            var className = $(val).attr('class').split(' ')[0];
            $.validator.addMethod(
                className,
                function (v, elem) {
                    var isValid = true;
                    var columnId = $(elem).parents().children('[id^=itemDiv_]').attr('id');
                    $('#' + columnId).find('.mage-error').each(function (el, val) {
                        if ($(val).css('display') != 'none') {
                            isValid = false;
                        }
                    });
                    return isValid;
                },
                $.mage.__("Please fill required fields in popup")
            );
        });
    });
});
