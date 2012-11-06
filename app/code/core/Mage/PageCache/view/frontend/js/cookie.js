/**
 * {license_notice}
 *
 * @category    PageCache
 * @package     js
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function ($) {
    $.widget('mage.noCacheCookie', {
        options: {
            expires: $.cookie.defaults.expires
        },

        /**
         * Set cookie by name with calculated expiration based on cookie lifetime.
         * @private
         */
        _create: function() {
            if (this.options.lifetime > 0) {
                this.options.expires = new Date();
                this.options.expires.setTime(
                    this.options.expires.getTime() + this.options.lifetime * 1000);
            }
            $.mage.cookies.set(this.options.name, 1, this.options.expires);
        }
    });
})(jQuery);
