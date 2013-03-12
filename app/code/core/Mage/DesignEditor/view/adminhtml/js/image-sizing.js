/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true*/
(function ($) {
    $.widget('vde.vdeImageSizing', {
        options: {
            restoreDefaultDataEvent: 'restoreDefaultData',
            saveFormEvent: 'saveForm',
            maxSizeValue: 500,
            formUrl: null,
            formId: null,
            messagesContainer: null
        },

        /**
         * Initialize widget
         * @private
         */
        _create: function() {
            this._bind();
        },

        /**
         * Bind event handlers
         * @private
         */
        _bind: function() {
            var body = $('body');
            body.on(this.options.restoreDefaultDataEvent, $.proxy(this._onRestoreDefaultData, this));
            body.on(this.options.saveFormEvent, $.proxy(this._onSaveForm, this));
            $(this.options.formId + " input[type='text']").live('keyup',  $.proxy(this._validateInput, this));
        },

        /**
         * Validate width and height input
         * @param event
         * @param data
         * @private
         */
        _validateInput: function(event, data)
        {
            var value = $(event.currentTarget).val();
            value = parseInt(value);
            value = isNaN(value) ? '' : value;
            value = value > this.options.maxSizeValue ? this.options.maxSizeValue : value;
            $(event.currentTarget).val(value);
        },

        /**
         * Restore default data for one item
         * @param event
         * @param data
         * @private
         */
        _onRestoreDefaultData: function(event, data) {
            for (var elementId in data) {
                $(document.getElementById(elementId)).val(data[elementId] ? data[elementId] : '');
            }
        },

        /**
         * Ajax saving form
         * @param event
         * @param data
         * @private
         */
        _onSaveForm: function(event, data) {
            $.ajax({
                url: this.options.formUrl,
                type: 'POST',
                data: $(this.options.formId).serialize(),
                dataType: 'json',
                showLoader: false,
                success: $.proxy(function(response) {
                    if (response.message_html) {
                        $(this.options.messagesContainer).append(response.message_html);
                    }
                    this.element.trigger('refreshIframe');
                }, this),
                error: $.proxy(function() {
                    alert($.mage.__('Error: unknown error.'));
                }, this)
            });
        }
    });
})(jQuery);
