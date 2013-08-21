/**
 * {license_notice}
 *
 * @category    frontend Persistent remember me popup
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function ($) {
    $.widget('mage.rememberMePopup', {
        options: {
            closeBtn: '.btn-close',
            windowOverlayTemplate: '<div class="window-overlay"></div>',
            popupBlockTemplate: '<div class="popup-block block popup-remember-tip active">' +
                                '<div class="block-title">' +
                                    '<strong>${title}</strong>' +
                                    '<div class="btn-close"></div>' +
                                '</div>' +
                                '<div class="block-content">' +
                                    '<p>${content}</p>' +
                                    '<div class="buttons-set">' +
                                        '<button class="button btn-close" type="button">' +
                                            '<span>' +
                                                '<span>Close</span>' +
                                            '</span>' +
                                        '</button>' +
                                    '</div>' +
                                '</div>' +
                            '</div>'
        },

        _create: function() {
            this._renderWindowOverLay();
            this._renderPopupBlock();
            $('body').append(this.windowOverlay.hide());
            $('body').append(this.popupBlock.hide());
            this.element.find('a').on('click', $.proxy(this._showPopUp, this));
        },

        /**
         * Add windowOverlay block to body
         * If windowOverlay is not an option, use default template
         * @private
         */
        _renderWindowOverLay: function() {
            if (this.options.windowOverlay) {
                this.windowOverlay = $(this.options.windowOverlay);
            } else {
                $.template('windowOverlayTemplate', this.options.windowOverlayTemplate);
                this.windowOverlay = $.tmpl('windowOverlayTemplate').hide();
            }
            this.windowOverlay.height($('body').height());
        },

        /**
         * Add popupBlock to body
         * If popupBlock is not an option, use default template
         * @private
         */
        _renderPopupBlock: function() {
            if (this.options.popupBlock) {
                this.popupBlock = $(this.options.popupBlock);
            } else {
                $.template('popupBlockTemplate', this.options.popupBlockTemplate);
                this.popupBlock = $.tmpl('popupBlockTemplate',
                    {title: this.options.title, content: this.options.content});
            }
            this.popupBlock.find(this.options.closeBtn).on('click', $.proxy(this._hidePopUp, this));
        },

        /**
         * show windowOverlay and popupBlock
         * @private
         */
        _showPopUp: function() {
            this.windowOverlay.show();
            this.popupBlock.show();
        },

        /**
         * hide windowOverlay and popupBlock
         * @private
         */
        _hidePopUp: function() {
            this.windowOverlay.hide();
            this.popupBlock.hide();
        }
    });
})(jQuery);