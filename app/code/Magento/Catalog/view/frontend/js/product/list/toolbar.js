/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function($) {
    /**
     * ProductListToolbarForm Widget - this widget is setting cookie and submitting form according to toolbar controls
     */
    $.widget('mage.productListToolbarForm', {

        options: {
            modeLink: '[data-role="mode-switcher"]',
            directionLink: '[data-role="direction-switcher"]',
            orderSelect: '[data-role="sorter"]',
            limitSelect: '[data-role="limiter"]',
            modeCookie: 'product_list_mode',
            directionCookie: 'product_list_dir',
            orderCookie: 'product_list_order',
            limitCookie: 'product_list_limit',
            postData: {}
        },

        _create: function() {
            $(this.options.modeLink).on(
                'click',
                {cookieName: this.options.modeCookie},
                $.proxy(this._processLink, this)
            );
            $(this.options.directionLink).on(
                'click',
                {cookieName: this.options.directionCookie},
                $.proxy(this._processLink, this)
            );
            $(this.options.orderSelect).on(
                'change',
                {cookieName: this.options.orderCookie},
                $.proxy(this._processSelect, this)
            );
            $(this.options.limitSelect).on(
                'change',
                {cookieName: this.options.limitCookie},
                $.proxy(this._processSelect, this)
            );
        },

        _processLink: function(event) {
            event.preventDefault();
            this._setCookie(event.data.cookieName, $(event.currentTarget).data('value'));
            $.mage.dataPost().postData(this.options.postData);
        },

        _processSelect: function(event) {
            this._setCookie(
                event.data.cookieName,
                event.currentTarget.options[event.currentTarget.selectedIndex].value
            );
            $.mage.dataPost().postData(this.options.postData);
        },

        _setCookie: function(cookieName, cookieValue) {
            $.cookie(cookieName, cookieValue, {path: '/'});
        }
    });
})(jQuery);