/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function($) {
    "use strict";
    /**
     * An auxiliary widget
     * Wraps gallery into dialog widget and opens the dialog in fullscreen mode
     */
    $.widget('mage.galleryFullScreen', {
        options: {
            selectors: {
                trigger: '[data-role=zoom-image], [data-role=zoom-track]'
            },
            fullscreenClass: 'magento-zoom-enlarged lightbox'
        },

        /**
         * Widget constructor
         * @protected
         */
        _create: function() {
            this._bind();
        },

        /**
         * Bind full screen handler
         * @protected
         */
        _bind: function() {
            var events = {};
            events['click ' + this.options.selectors.trigger] = '_fullScreen';
            this._on(events);
        },

        /**
         * Open gallery in dialog
         * @param {Object} e - event object
         */
        _fullScreen: function() {
            this.element
                .gallery('option', {showNotice: false, fullSizeMode: true, showButtons: true})
                .dialog({
                    resizable: false,
                    dialogClass: this.options.fullscreenClass,
                    close: $.proxy(function() {
                        this.element
                            .gallery('option', {showNotice: true, fullSizeMode: false, showButtons: false})
                            .dialog('destroy').show();
                    }, this)
                });
        }
    });
})(jQuery);