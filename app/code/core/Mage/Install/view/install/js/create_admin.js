/**
 * {license_notice}
 *
 * @category    install admin
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true */
(function ($) {
    $(document).ready(function () {
        $('#form-validate').mage().validate({errorClass: 'mage-error', errorElement: 'div'});
    });
}(jQuery));
