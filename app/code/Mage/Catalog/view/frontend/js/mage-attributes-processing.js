/**
 * {license_notice}
 *
 * @category    frontend poll
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint evil:true browser:true jquery:true*/
(function ($) {
    $(document).ready(function () {
        $('[data-mage-redirect]').each(function () {
            var data = eval("(" + $(this).attr('data-mage-redirect') + ")");
            $(this).on(data.event, function () {
                if (data.url) {
                    location.href = data.url;
                } else {
                    location.href = $(this).val();
                }
            });
        });
    });
}(jQuery));
