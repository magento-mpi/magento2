/**
 * {license_notice}
 *
 * @category    mage
 * @package     captcha
 * @copyright   {copyright}
 * @license     {license_link}
 */

/*jshint browser:true jquery:true*/

(function ($) {
    $(document).ready(function () {
        var captcha = {
            url: null,
            formSelector: null,
            imageSelector: null,
            refreshClass: 'refreshing'
        };
        $.mage.event.trigger("mage.captcha.initialize", captcha);
        $(captcha.imageSelector).on('click', function () {
            $(captcha.imageSelector).addClass(captcha.refreshClass);
            $.ajax({
                url: captcha.url,
                type: 'post',
                dataType: 'json',
                data: {'formId': captcha.formSelector.replace(/^(#|.)/, "")},
                success: function (response) {
                    if (response.imgSrc) {
                        $(captcha.formSelector).attr('src', response.imgSrc);
                    }
                    $(captcha.imageSelector).removeClass(captcha.refreshClass);
                },
                error: function () {
                    $(captcha.imageSelector).removeClass(captcha.refreshClass);
                }
            });
        });
    });
})(jQuery);


