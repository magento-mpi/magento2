/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function($) {
    /**
     * Widget vde frame
     */
    $.widget('vde.vdeFrame', {
        options: {
            vdeToolbar: null,
            vdeFooter: null
        },

        _create: function () {
            this._bind();
            this._initFrame();
        },

        _bind: function() {
            $(window).on('resize', $.proxy(this._resizeFrame, this));
            $('body').on('refreshIframe', $.proxy(this._refreshFrame, this));
        },

        _resizeFrame: function() {
            var windowHeight = $(window).innerHeight(),
            vdeToolbarHeight = $(this.options.vdeToolbar).height(),
            vdeFooterHeight = $(this.options.vdeToolbar).height(),
            frameHeight = windowHeight - vdeToolbarHeight - vdeFooterHeight;
            this.element.height(frameHeight);
        },

        _refreshFrame: function() {
            this.element[0].contentWindow.location.reload(true);
        },

        _initFrame: function() {
            this._resizeFrame();
        }
    });
})( jQuery );
