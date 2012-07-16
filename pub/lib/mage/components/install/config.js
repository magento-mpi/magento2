/**
 * {license_notice}
 *
 * @category    install locale
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */

/*jshint eqnull:true */
(function ($) {
    $(document).ready(function () {
        $('#form-validate').mage().validate();
        $('#use_secure').on('click', function () {
            return this.checked ? $('#use_secure_options').show() : $('#use_secure_options').hide();
        });
    });
}(jQuery));