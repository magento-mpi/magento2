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
    'use strict';
    /**
     * VDE revert theme button widget
     */
    $.widget('vde.vde-edit-button', $.ui.button, {
        options: {
            dialogSelector:  '#dialog-message-confirm',
            eventData: {
                url: undefined
            }
        },

        /**
         * Element creation
         * @protected
         */
        _create: function() {
            this._bind();
            this._super();
        },

        /**
         * Bind handlers
         * @protected
         */
        _bind: function() {
            this.element.on('click.vde-edit-button',  $.proxy(this._onRevertEvent, this));
            $('body').on('refreshIframe', $.proxy(this._enableButton, this));
        },

        /**
         * Handler for 'revert-to-last' and 'revert-to-default' event
         * @private
         */
        _onRevertEvent: function() {
            if (this.element.hasClass('disabled')) {
                return false;
            }
            var dialog = this._getDialog();
            if (this.options.eventData.confirm_message && dialog) {
                this._showConfirmMessage(dialog, $.proxy(this._sendRevertRequest, this));
            } else {
                this._sendRevertRequest();
            }
        },

        /**
         * Show confirmation message if it was assigned
         * @private
         */
        _showConfirmMessage: function(dialogElement, callback) {
            var dialog = dialogElement.data('dialog');
            var buttons = {
                text: $.mage.__('Ok'),
                click: callback,
                'class': 'primary'
            };
            if (this.options.eventData.confirm_message) {
                dialog.text.set(this.options.eventData.confirm_message);
                dialog.setButtons(buttons);
                dialog.open();
            }
        },

        /**
         * Sent request to revert changes
         * @private
         */
        _sendRevertRequest: function() {
            $.ajax({
                url: this.options.eventData.url,
                type: 'GET',
                dataType: 'JSON',
                async: false,
                success: $.proxy(function(data) {
                    if (data.error) {
                        throw Error($.mage.__('Some problem with revert action'));
                        return;
                    }
                    document.location.reload();
                }, this),
                error: function() {
                    throw Error($.mage.__('Some problem with revert action'));
                }
            });
        },

        /**
         * Enable button
         * @private
         */
        _enableButton: function() {
            this.element.removeClass('disabled');
        },

        /**
         * Get dialog element
         *
         * @returns {*|HTMLElement}
         * @private
         */
        _getDialog: function() {
            return $(this.options.dialogSelector);
        }
    });
})(jQuery);
