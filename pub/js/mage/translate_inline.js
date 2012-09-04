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
                        click: this.formOk.bind(this)
                    },
                    {
                        text: 'Close',
                        class: 'form-button button',
                        click: function() {
                            jQuery(this).dialog("close");
                        }
                    }],
                close: this.formClose.bind(this)
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

        var content = '<form id="translate-inline-form">';
        var t = new Template(
            '<div class="magento_table_container"><table cellspacing="0">' +
                '<tr><th class="label">Location:</th><td class="value">#{location}</td></tr>' +
                '<tr><th class="label">Scope:</th><td class="value">#{scope}</td></tr>' +
                '<tr><th class="label">Shown:</th><td class="value">#{shown_escape}</td></tr>' +
                '<tr><th class="label">Original:</th><td class="value">#{original_escape}</td></tr>' +
                '<tr><th class="label">Translated:</th><td class="value">#{translated_escape}</td></tr>' +
                '<tr><th class="label"><label for="perstore_#{i}">Store View Specific:</label></th><td class="value">' +
                    '<input id="perstore_#{i}" name="translate[#{i}][perstore]" type="checkbox" value="1"/>' +
                '</td></tr>' +
                '<tr><th class="label"><label for="custom_#{i}">Custom:</label></th><td class="value">' +
                    '<input name="translate[#{i}][original]" type="hidden" value="#{scope}::#{original_escape}"/>' +
                    '<input id="custom_#{i}" name="translate[#{i}][custom]" class="input-text" value="#{translated_escape}" />' +
                '</td></tr>' +
            '</table></div>'
        );
        for (i = 0; i < data.length; i++) {
            data[i]['i'] = i;
            data[i]['shown_escape'] = this.escapeHTML(data[i]['shown']);
            data[i]['translated_escape'] = this.escapeHTML(data[i]['translated']);
            data[i]['original_escape'] = this.escapeHTML(data[i]['original']);
            content += t.evaluate(data[i]);
        }
        content += '</form><p class="a-center accent">Please refresh the page to see your changes after submitting this form.</p>';

        this.translateDialog
            .html(content)
            .dialog('open');
        this.trigHide();
    },

    formOk: function(win) {
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

        new Ajax.Request(this.ajaxUrl, {
            method: 'post',
            parameters: parameters,
            onComplete: this.ajaxComplete.bind(this, win)
        });

        this.formIsSubmitted = false;
    },

    ajaxComplete: function(win, transport) {
        jQuery(win).dialog('close');
        this.formClose(win);
    },

    formClose: function(win) {
        jQuery(win).html('');
        this.formIsShown = false;
    },

    escapeHTML: function(str) {
        this.helperDiv.innerHTML = '';
        var text = document.createTextNode(str);
        this.helperDiv.appendChild(text);
        var escaped = this.helperDiv.innerHTML;
        escaped = escaped.replace(/"/g, '&quot;');
        return escaped;
    }
}
