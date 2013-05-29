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
            eventData: {}
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
        },

        /**
         * Handler for 'revert-to-last' and 'revert-to-default' event
         *
         * @private
         */
        _onRevertEvent: function() {
            $.ajax({
                url: this.options.eventData.url,
                type: 'POST',
                dataType: 'JSON',
                async: false,
                success: function(data) {
                    if (data.error) {
                        /** @todo add error validator */
                        throw Error($.mage.__('Some problem with revert action'));
                        return;
                    }
                },
                error: function(data) {
                    throw Error($.mage.__('Some problem with revert action'));
                }
            });
        }
    });
})(jQuery);
