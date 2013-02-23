/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function ($) {
    $.widget('vde.vdeImageSizing', {
        options: {
            restoreDefaultDataEvent: 'restoreDefaultData',
            saveFormEvent: 'saveForm',
            formUrl: '',
            formId: ''
        },

        _create: function() {
            this._bind();
        },

        _bind: function() {
            var body = $('body');
            body.on(this.options.restoreDefaultDataEvent, $.proxy(this._onRestoreDefaultData, this));
            body.on(this.options.saveFormEvent, $.proxy(this._onSaveForm, this));
        },

        _onRestoreDefaultData: function(event, data) {
            for (var elementId in data) {
                $("#" + elementId).val(data[elementId]);
            }
        },

        _onSaveForm: function(event, data){
            $.ajax({
                url: this.options.formUrl,
                type: 'POST',
                data: $(this.options.formId).serialize(),
                dataType: 'json',
                showLoader: false,
                success: $.proxy(function(response) {
                    if (response.message_html) {
                        $('#vde-tab-imagesizing-messages-placeholder').append(response.message_html);
                    }
                }, this),
                error: $.proxy(function() {
                    alert($.mage.__('Error: unknown error.'));
                }, this)
            });
        }
    });
})(jQuery);
