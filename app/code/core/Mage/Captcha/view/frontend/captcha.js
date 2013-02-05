/**
 * {license_notice}
 *
 * @category    mage
 * @package     captcha
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function($, undefined) {
    "use strict";
    $.widget('mage.captcha', {
        options: {
            refreshClass: 'refreshing',
            reloadSelector: '.captcha-reload',
            imageSelector: '.captcha-img'
        },
        _create: function() {
            this.element.on('click', this.options.reloadSelector, $.proxy(this.refresh, this));
        },
        refresh: function(e) {
            var image = $(e.currentTarget);
            image.addClass(this.options.refreshClass);
            $.ajax({
                url: this.options.url,
                type: 'post',
                dataType: 'json',
                context: this,
                data: {
                    'formId': this.options.type
                },
                success: function (response) {
                    if (response.imgSrc) {
                        this.element.find(this.options.imageSelector).attr('src', response.imgSrc);
                    }
                },
                complete: function() {
                    image.removeClass(this.options.refreshClass);
                }
            });
        }
    });
})(jQuery);

