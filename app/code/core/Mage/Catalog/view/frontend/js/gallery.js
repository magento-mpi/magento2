/**
 * {license_notice}
 *
 * @category    catalog product gallery image
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true, jquery:true*/
/*global window:true*/
(function($) {
    "use strict";
    $(function() {
        $('div.buttons-set > a.button').on('click', function() {
            window.close();
            return false;
        });
        var img = $('#product-gallery-image'),
            width = img.width() > 300 ? 300 : img.width();
        window.resizeTo(width + 90, img.height() + 210);
    });
})(jQuery);
