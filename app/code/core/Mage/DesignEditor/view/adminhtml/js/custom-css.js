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
            btnUpdateCss: '.vde-tools-content .action-update'
        },

        _create: function() {
            this.btnCssUpdate = $(this.options.btnUpdateCss);
            this._events();
        },

        _events: function() {
            this.btnCssUpdate.on('click', $.proxy(this._updateCustomCss, this));
        },

        _updateCustomCss: function()
        {
            $.ajax({
                type: 'POST',
                url:  this.options.saveCustomCssUrl,
                data: {custom_css_content: $(this.options.customCssCode).val()},
                dataType: 'json',
                success: $.proxy(function(response) {
                    if (response.message_html) {
                        $('#vde-tab-custom-messages-placeholder').append(response.message_html);
                    }
                }, this),
                error: function() {
                    alert($.mage.__('Error: unknown error.'));
                }
            });
        }
    });
})(window.jQuery);
