/**
 * {license_notice}
 *
 * @category    pub
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint evil:true browser:true jquery:true*/
(function ($) {
    $(document).ready(function () {
        $('[data-mage-popwin]').each(function () {
            $(this).popupWindow(eval("(" + $(this).attr('data-mage-popwin') + ")"));
        });
    });
}(jQuery));