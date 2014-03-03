/**
 * {license_notice}
 *
 * @category    mage
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint evil:true browser:true jquery:true */
(function ($) {
    $.widget('mage.requireCookie', {
        options: {
            event: 'click',
            noCookieUrl: 'enable-cookies',
            triggers: ['.action.login', '.action.submit']
        },

        /**
         * Constructor
         * @private
         */
        _create: function() {
            this._bind();
        },

        /**
         * This method binds elements found in this widget.
         * @private
         */
        _bind: function() {
            var events = {};
            $.each(this.options.triggers, function(index, value) {
                events['click ' + value] = '_checkCookie';
            });
            this._on(events);
        },

        /**
         * This method set the url for the redirect.
         * @private
         */
        _checkCookie: function(event) {
            $.cookie("test", 1);
            if ($.cookie("test")) {
                $.cookie("test", null);
            } else {
                event.preventDefault();
                window.location = this.options.noCookieUrl;
            }
        }
    });
})(jQuery);
