/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/*jshint jquery:true*/
(function($) {
    /**
     * Widget vde frame
     */
    $.widget('vde.vdeFrame', {
        options: {
            vdeToolbar: null,
            vdePanel: null
        },

        _create: function () {
            this._bind();
            this._initFrame();
        },

        _bind: function() {
            $(window).on('resize', $.proxy(this._resizeFrame, this));
            $('body').on('refreshIframe', $.proxy(this._refreshFrame, this));
            this.element[0].on('load', function() {
                $('body').trigger('processStop');
            });
        },

        /**
         * Calculate and set frame height
         *
         * @private
         */
        _resizeFrame: function() {
            var windowHeight = $(window).innerHeight(),
                vdeToolbarHeight = $(this.options.vdeToolbar).outerHeight(true),
                vdePanelHeight = $(this.options.vdePanel).outerHeight(true),
                frameHeight = windowHeight - vdeToolbarHeight - vdePanelHeight;

            this.element.height(frameHeight);
        },

        /**
         * Reload frame
         *
         * @private
         */
        _refreshFrame: function() {
            // Timeout with 0 millisecond delay used because _refreshFrame is
            // usually called within the context of an AJAX request's
            // success/failure callback.
            //
            // If the timeout is removed and just trigger called, then
            // the AJAX code will call trigger('processStop') in the event
            // loop and stop the loader spinner from being shown.
            window.setTimeout(function() {
                $('body').trigger('processStart');
            }, 0);

            this.element[0].contentWindow.location.reload(true);
        },

        /**
         * Initialize frame
         *
         * @private
         */
        _initFrame: function() {
            this._resizeFrame();
        }
    });
})( jQuery );
