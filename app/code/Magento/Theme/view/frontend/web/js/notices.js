/**
 * {license_notice}
 *
 * @category    notices
 * @package     js
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function ($) {
    $.widget('mage.cookieBlock', {
        _create: function() {
            this.element.show();
            $(this.options.cookieAllowButtonSelector).on('click', $.proxy(function() {
                var cookieExpires = new Date(new Date().getTime() + this.options.cookieLifetime * 1000);
                $.mage.cookies.set(this.options.cookieName, this.options.cookieValue, {expires: cookieExpires});
                if ($.mage.cookies.get(this.options.cookieName)) {
                    window.location.reload();
                } else {
                    window.location.href = this.options.noCookiesUrl;
                }
            }, this));
        }
    });
})(jQuery);
