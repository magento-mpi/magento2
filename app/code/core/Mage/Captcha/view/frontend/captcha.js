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
            this.element.on('click', $.proxy(this.refresh, this));
        },
        refresh: function () {
            this.element.addClass(this.options.refreshClass);
            $.ajax({
                url: this.options.url,
                type: 'post',
                dataType: 'json',
                context: this,
                data: {'formId': this.options.formSelector.replace(/^(#|.)/, "")},
                success: function (response) {
                    if (response.imgSrc) {
                        $(this.options.formSelector).attr('src', response.imgSrc);
                    }
                    this.element.removeClass(this.options.refreshClass);
                },
                error: function () {
                    this.element.removeClass(this.options.refreshClass);
                }
            });
        }
    })
})(jQuery);

