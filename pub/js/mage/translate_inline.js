/**
 * {license_notice}
 *
 * @category    Mage
 * @package     js
 * @copyright   {copyright}
 * @license     {license_link}
 */

var TranslateInline = Class.create();
TranslateInline.prototype = {
    initialize: function(trigEl, ajaxUrl, area) {
        this.ajaxUrl = ajaxUrl.replace('admin', 'backend/admin');
        this.area = area;

        this.translateDialog = jQuery('<div />', {id: 'translate-inline'})
            .prependTo('body')
            .dialog({
                draggable: true,
                resizable: true,
                modal: true,
                dialogClass: "dialog",
                title: "Translation",
                width: 650,
                height: 470,
                zIndex: 2100,
                position: 'center',
                buttons : [{
                        text: 'Submit',
                        class: 'form-button button',
                        click: jQuery.proxy(this.formOk, this)
                    },
                    {
                        text: 'Close',
                        class: 'form-button button',
                        click: function() {
                            jQuery(this).dialog("close");
                        }
                    }],
                close: jQuery.proxy(this.formClose, this)
            })
            .dialog('close');
        jQuery(document).editTrigger();
        jQuery(document).on('edit.editTrigger', jQuery.proxy(this.formShow, this));
    },

    formShow: function(e) {
        if (this.formIsShown) {
            return;
        }
        this.formIsShown = true;

        eval('var data = ' + e.target.getAttribute('translate'));
        jQuery.template("translateInline", '<form id="translate-inline-form">' +
                '{{each(i, item) data}}' +
                '<div class="magento_table_container"><table cellspacing="0">' +
                    '{{each item}}' +
                        '<tr><th class="label" style="text-transform: capitalize;">${$index}:</th><td class="value">${$value}</td></tr>' +
                    '{{/each}}' +
                    '<tr><th class="label"><label for="perstore_${i}">Store View Specific:</label></th><td class="value">' +
                        '<input id="perstore_${i}" name="translate[${i}][perstore]" type="checkbox" value="1"/>' +
                    '</td></tr>' +
                    '<tr><th class="label"><label for="custom_${i}">Custom:</label></th><td class="value">' +
                        '<input name="translate[${i}][original]" type="hidden" value="${item.scope}::${escape(item.original)}"/>' +
                        '<input id="custom_${i}" name="translate[${i}][custom]" class="input-text" value="${escape(item.translated)}" />' +
                    '</td></tr>' +
                '</table></div>' +
                '{{/each}}' +
            '</form><p class="a-center accent">Please refresh the page to see your changes after submitting this form.</p>'
        );

        this.translateDialog
            .html(jQuery.tmpl("translateInline", {
                data: data,
                escape:this.escapeHTML
            }))
            .dialog('open');
    },

    formOk: function() {
        if (this.formIsSubmitted) {
            return;
        }
        this.formIsSubmitted = true;

        var inputs = $('translate-inline-form').getInputs(), parameters = {};
        for (var i = 0; i < inputs.length; i++) {
            if (inputs[i].type == 'checkbox') {
                if (inputs[i].checked) {
                    parameters[inputs[i].name] = inputs[i].value;
                }
            }
            else {
                parameters[inputs[i].name] = inputs[i].value;
            }
        }
        parameters['area'] = this.area;
        jQuery.ajax({
            url: this.ajaxUrl,
            type: 'POST',
            data: parameters,
            context: this.translateDialog
        }).complete(jQuery.proxy(this.ajaxComplete, this));

        this.formIsSubmitted = false;
    },

    ajaxComplete: function() {
        this.translateDialog.dialog('close');
    },

    formClose: function() {
        if(this.translateDialog) {
            this.translateDialog.empty();
        }
        this.formIsShown = false;
    },

    escapeHTML: function(str) {
        return str ?
            jQuery('<div/>').text(str).html().replace(/"/g, '&quot;'):
            false;
    }
};