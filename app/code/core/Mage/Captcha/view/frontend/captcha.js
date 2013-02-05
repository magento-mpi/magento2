/**
 * {license_notice}
 *
 * @category    mage
 * @package     captcha
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function ($, undefined) {
    $.widget('mage.captcha', {
        options: {
            refreshClass: 'refreshing'
        },
        _create: function () {
            this.element.on('click', '.captcha-reload' , $.proxy(this.refresh, this));
        },
        refresh: function (e) {
            var image = $(e.currentTarget);
            image.addClass(this.options.refreshClass);
            $.ajax({
                url: this.options.url,
                type: 'post',
                dataType: 'json',
                context: this,
                data: {'formId': this.options.type},
                success: function (response) {
                    if (response.imgSrc) {
                        this.element.find(".captcha-img").attr('src', response.imgSrc);
                    }
                    image.removeClass(this.options.refreshClass);
                },
                error: function () {
                    image.removeClass(this.options.refreshClass);
                }
            });
        }
    });
})(jQuery);

