/**
 * {license_notice}
 *
 * @category    cookie
 * @package     js
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function ($) {
    $.widget('mage.cookieInit', {
        options: {
            expires: null,
            path: '/',
            domain: 'document.domain',
            secure: false
        },

        /**
         * Extend jQuery cookie plugin default options with widget options.
         * @private
         */
        _create: function() {
            $.extend($.cookie.defaults, this.options);
        }
    });
})(jQuery);