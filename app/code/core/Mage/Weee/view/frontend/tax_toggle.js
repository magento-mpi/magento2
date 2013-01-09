/**
 * {license_notice}
 *
 * @category    Varien
 * @package     js
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function($){
    "use strict";
    $(function(){
        $('body').on('click','[data-tax-toggle]',function(){
            var currElem = $(this),
                args = currElem.data("tax-toggle");
            $(this).toggleClass(args.expandedClassName);
            $('#'+ args.itemTaxId).toggle();
        });
    });
})(jQuery);

/**
 * NEED TO REMOVE this function once all the references of taxToggle are removed
 */
function taxToggle(details, switcher, expandedClassName)
{
    if ($(details).style.display == 'none') {
        $(details).show();
        $(switcher).addClassName(expandedClassName);
    } else {
        $(details).hide();
        $(switcher).removeClassName(expandedClassName);
    }
}
