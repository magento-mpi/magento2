/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Cms
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
        })
    });
})( jQuery );
