/**
 * {license_notice}
 *
 * @category    Varien
 * @package     js
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function($) {
    "use strict";
    $(function() {
        $('body').on('click', '[data-tax-toggle]', function() {
            var currElem = $(this),
                args = currElem.data("tax-toggle"),
                expandedClassName = args.expandedClassName ? args.expandedClassName : 'cart-tax-total-expanded';
            currElem.toggleClass(expandedClassName);
            $(args.itemTaxId).toggle();
        });
    });
})(jQuery);
