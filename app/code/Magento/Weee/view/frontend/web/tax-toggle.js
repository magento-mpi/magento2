/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
define(["jquery"], function($){
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

});