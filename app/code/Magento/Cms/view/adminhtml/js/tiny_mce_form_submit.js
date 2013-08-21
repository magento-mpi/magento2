/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Fix for issue MAGETWO-8415
 */
(function($) {
    $(document).ready(function() {
        $(document).on("beforeSubmit", function(event) {
            return event.target.submit();
        });
    });
})( jQuery );
