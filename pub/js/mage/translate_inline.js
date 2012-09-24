/**
 * {license_notice}
 *
 * @category    Mage
 * @package     js
 * @copyright   {copyright}
 * @license     {license_link}
 */
(function($) {
    $.widget("mage.translateInline", {
        options: {
            translateForm: {
                template: '<form id="${data.id}">{{each(i, item) data.items}}' +
                    '<div class="magento_table_container"><table cellspacing="0">' +
                    '{{each item}}' +
                    '<tr><th class="label" style="text-transform: capitalize;">${$index}:</th>' +
                    '<td class="value">${$value}</td></tr>' +
                    '{{/each}}' +
                    '<tr><th class="label"><label for="perstore_${i}">Store View Specific:</label></th>' +
                    '<td class="value">' +
                    '<input id="perstore_${i}" name="translate[${i}][perstore]" type="checkbox" value="1"/>' +
                    '</td></tr>' +
                    '<tr><th class="label"><label for="custom_${i}">Custom:</label></th><td class="value">' +
                    '<input name="translate[${i}][original]" type="hidden" value="${item.scope}::${escape(item.original)}"/>' +
                    '<input id="custom_${i}" name="translate[${i}][custom]" class="input-text" value="${escape(item.translated)}" />' +
                    '</td></tr></table></div>' +
                    '{{/each}}</form>{{if data.message}}<p class="a-center accent">${data.message}</p>{{/if}}',
                data: {
                    id: 'translate-inline-form',
                    message: 'Please refresh the page to see your changes after submitting this form.'
                }
            },
            dialog: {
                modal: true,
                dialogClass: "dialog",
                width: 650,
                title: 'Translate',
                height: 470,
                zIndex: 2100,
                buttons : [{
                    text: 'Submit',
                    'class': 'form-button button'
                },
                {
                    text: 'Close',
                    'class': 'form-button button'
                }]
            }
        },
        /**
         * Translate Inline creation
         * @protected
         */
        _create: function() {
            this._initTranslateDialog();
            this._initEditTrigger();
            this._bind();
        },
        /**
         * Bind on edit event
         * @protected
         */
        _bind: function() {
            this.element.on('edit.editTrigger', $.proxy(this._onEdit, this));
        },
        /**
         * Initialization of Translate Inline dialog
         * @protected
         */
        _initTranslateDialog: function() {
            this.translateDialog = jQuery('<div />', {id: this.options.dialog.id})
                .prependTo('body')
                .dialog($.extend(true, {
                    buttons : [
                        {click: $.proxy(this._formSubmit, this)},
                        {click: function() {$(this).dialog("close");}}
                    ],
                    close: $.proxy(this._formClose, this)
                }, this.options.dialog))
                .dialog('close');
        },
        /**
         * Initialisation of Edit Trigger
         * @protected
         */
        _initEditTrigger: function() {
            this.element.editTrigger(this.options.editTrigger || {});
        },
        /**
         * Render translation form and open dialog
         * @param {Object} event object
         * @protected
         */
        _onEdit: function(e) {
            if (this.formIsShown) {
                return;
            }
            this.formIsShown = true;
            $.template("translateInline", this.options.translateForm.template);

            this.translateDialog
                .html($.tmpl("translateInline", {
                data: $.extend({items: $(e.target).data('translate')},
                    this.options.translateForm.data),
                escape: $.mage.escapeHTML
            })).dialog('open');
        },
        /**
         * Send ajax request on form submit
         * @protected
         */
        _formSubmit: function() {
            if (this.formIsSubmitted) {
                return;
            }
            this.formIsSubmitted = true;

            var parameters = jQuery.param({area: this.options.area}) +
                '&' + jQuery('#' + this.options.translateForm.data.id).serialize();

            jQuery.ajax({
                url: this.options.ajaxUrl,
                type: 'POST',
                data: parameters,
                context: this.translateDialog
            }).complete(jQuery.proxy(function() {
                this.translateDialog.dialog('close');
            }, this));

            this.formIsSubmitted = false;
        },
        /**
         * Clear dialog content on closing
         * @protected
         */
        _formClose: function() {
            if (this.translateDialog) {
                this.translateDialog.empty();
            }
            this.formIsShown = false;
        },
        /**
         * Destroy translateInline
         */
        destroy: function() {
            this.translateDialog.dialog('destroy');
            this.element.editTrigger('destroy');
            this.element.off('.editTrigger');
            return $.Widget.prototype.destroy.call(this);
        }
    });
    /*
     * @TODO move the "escapeHTML" method into the file with global utility functions
     */
    $.extend(true, $, {
        mage: {
            escapeHTML: function(str) {
                return str ?
                    jQuery('<div/>').text(str).html().replace(/"/g, '&quot;'):
                    false;
            }
        }
    });
})(jQuery);
