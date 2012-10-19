/**
 * {license_notice}
 *
 * @category    tab
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true */
(function ($) {
    $.widget('mage.tags', {
        options : {
        },
        _create: function() {
            this.element.mage().validate({errorClass: 'mage-error', errorElement: 'div'});
        }
    });
})(jQuery);
