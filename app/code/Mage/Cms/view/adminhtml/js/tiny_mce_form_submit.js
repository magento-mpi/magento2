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
        $('#edit_form').on('submit', function(event) {
            event.target.submit();
        })
    });
})( jQuery );
