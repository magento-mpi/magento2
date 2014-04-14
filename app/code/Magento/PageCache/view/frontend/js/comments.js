/**
 * Finds all comment elements
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true expr:true*/
(function ($) {
    "use strict";
    $.fn.comments = function () {
        var elements = [];
        var lookup = function (el) {
            el.contents().each(function (i, el) {
                if (el.nodeType == 8) {
                    elements.push(el);
                } else if (el.nodeType == 1) {
                    lookup($(el));
                }
            });
        };
        lookup(this);
        return elements;
    };
})(jQuery);