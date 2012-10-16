/**
 * {license_notice}
 *
 * @category    mage product view
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function ($) {
    $(document).ready(function () {
        var productView = {
            // Filled in initialization event
            recentlyViewedItemSelector: null
        };
        // Trigger initialize event
        $.mage.event.trigger('mage.productView.initialize', productView);
        $.mage.decorator.list(productView.recentlyViewedItemSelector);
    });
})(jQuery);
