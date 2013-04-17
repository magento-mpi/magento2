/**
 * {license_notice}
 *
 * @category    design
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true*/
(function($) {
    'use strict';
    $.widget('vde.customCssPanel', {
        options: {
            saveCustomCssUrl: null,
            customCssCode: '#custom_code',
            btnUpdateCss: '#vde-tab-custom .action-update',
            btnUpdateDownload: '#vde-tab-custom [data-file="uploaded-css"]'
        },

        updateButtons: function() {
            this._prepareUpdateButton();
        },

        _create: function() {
            this.btnCssUpdate = $(this.options.btnUpdateCss);
            this.btnCssDelete = $(this.options.btnDeleteCss);
            this.customCssCode = $(this.options.customCssCode);
            this.btnUpdateDownload = $(this.options.btnUpdateDownload);
            this._prepareUpdateButton();
            this._events();
        },

        _events: function() {
            this.btnCssUpdate.on('click', $.proxy(this._updateCustomCss, this));
            this.btnCssDelete.on('click', $.proxy(this._deleteCustomCss, this));
            this.customCssCode.on('input onchange change', $.proxy(this._editCustomCss, this));
        },

        _editCustomCss: function()
        {
            if ($.trim($(this.customCssCode).val())) {
                this.btnCssUpdate.removeAttr('disabled');
            }
        },

        _postUpdatedCustomCssContent: function()
        {
            $.ajax({
                type: 'POST',
                url:  this.options.saveCustomCssUrl,
                data: {custom_css_content: $(this.customCssCode).val()},
                dataType: 'json',
                success: $.proxy(function(response) {
                    this.element.trigger('addMessage', {
                        containerId: '#vde-tab-custom-messages-placeholder',
                        message: response.message
                    });
                    this.element.trigger('refreshIframe');
                    this._prepareUpdateButton();
                }, this),
                error: function() {
                    alert($.mage.__('Error: unknown error.'));
                }
            });
        },

        _updateCustomCss: function()
        {
            this._postUpdatedCustomCssContent();
        },

        _deleteCustomCss: function()
        {
            $(this.customCssCode).val('');
            this._postUpdatedCustomCssContent();
        },

        _prepareUpdateButton: function()
        {
            if (!$.trim($(this.customCssCode).val())) {
                this.btnCssUpdate.attr('disabled', 'disabled');
                $(this.btnUpdateDownload).addClass('no-display');
            } else {
                this.btnCssUpdate.removeAttr('disabled');
                $(this.btnUpdateDownload).removeClass('no-display');
            }
        }
    });
})(window.jQuery);
