/**
 * {license_notice}
 *
 * @category    gift card account
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true*/
(function($) {
    "use strict";
    $.widget('mage.payment', {
        /**
         * Process form elements, setting attribute values and showing/hiding their parent node.
         * @private
         */
        _create: function() {
            this.element[0].getElements().each(function(elm) {
                if (elm.name === 'payment[method]' && elm.value === 'free') {
                    // Show 'No Payment Information Required' with checked radio button.
                    $(elm).attr('checked', true).attr('disabled', false).parent().show();
                } else {
                    // Show 'Check / Money order' with no radio button.
                    $(elm).parent().attr('disabled', true).hide();
                }
            });
        }
    });
})(jQuery);
