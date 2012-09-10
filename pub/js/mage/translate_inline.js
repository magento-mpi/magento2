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
        this.ajaxUrl = ajaxUrl;
        this.area = area;

        this.trigTimer = null;
        this.trigContentEl = null;
        if (Prototype.Browser.IE) {
            $$('*[translate]').each(this.initializeElement.bind(this));
            var scope = this;
            Ajax.Responders.register({ onComplete: function() {
                window.setTimeout(scope.reinitElements.bind(scope), 50)
            }
            });
            var ElementNode = (typeof HTMLElement != 'undefined' ? HTMLElement : Element)
            var ElementUpdate = ElementNode.prototype.update;
            ElementNode.prototype.update = function() {
                ElementUpdate.apply(this, arguments);
                $(this).select('*[translate]').each(scope.initializeElement.bind(scope));
            }
        }
        this.translateDialog = jQuery('<div id="translate-inline" />')
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
        this.trigEl = $(trigEl);
        this.trigEl.observe('click', this.formShow.bind(this));

        Event.observe(document.body, 'mousemove', function(e) {
            var target = Event.element(e);
            if (!$(target).match('*[translate]')) {
                target = target.up('*[translate]');
            }

            if (target && $(target).match('*[translate]')) {
                this.trigShow(target, e);
            } else {
                if (Event.element(e).match('#' + trigEl)) {
                    this.trigHideClear();
                } else {
                    this.trigHideDelayed();
                }
            }
        }.bind(this));

        this.helperDiv = document.createElement('div');
    },

    initializeElement: function(el) {
        if (!el.initializedTranslate) {
            el.addClassName('translate-inline');
            el.initializedTranslate = true;
        }
    },

    reinitElements: function(el) {
        $$('*[translate]').each(this.initializeElement.bind(this));
    },

    trigShow: function(el, event) {
        if (this.trigContentEl != el) {
            this.trigHideClear();
            this.trigContentEl = el;
            var p = Element.cumulativeOffset(el);

            this.trigEl.style.left = p[0] + 'px';
            this.trigEl.style.top = p[1] + 'px';
            this.trigEl.style.display = 'block';

            Event.stop(event);
        };
    },

    trigHide: function() {
        this.trigEl.style.display = 'none';
        this.trigContentEl = null;
    },

    trigHideDelayed: function() {
        if (this.trigTimer === null) {
            this.trigTimer = window.setTimeout(this.trigHide.bind(this), 2000);
        }
    },

    trigHideClear: function() {
        clearInterval(this.trigTimer);
        this.trigTimer = null;
    },

    formShow: function() {
        if (this.formIsShown) {
            return;
        }
        this.formIsShown = true;

        var el = this.trigContentEl;
        if (!el) {
            return;
        }
        this.trigHideClear();
        eval('var data = ' + el.getAttribute('translate'));
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
            .empty()
            .append(jQuery.tmpl("translateInline", {
                data: data,
                escape:this.escapeHTML
            }))
            .dialog('open');
        this.trigHide();
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
        })
            .complete(jQuery.proxy(this.ajaxComplete, this))
            .error(function() { alert("error"); });

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
}
