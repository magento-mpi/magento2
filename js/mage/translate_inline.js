/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Js
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

var TranslateInline = Class.create();
TranslateInline.prototype = {
    initialize: function(trigEl, ajaxUrl){
        this.ajaxUrl = ajaxUrl;

        this.trigTimer = null;
        this.trigContentEl = null;

        $$('*[translate]').each(this.initializeElement.bind(this));

        this.trigEl = $(trigEl);
        this.trigEl.observe('mouseover', this.trigHideClear.bind(this));
        this.trigEl.observe('mouseout', this.trigHideDelayed.bind(this));
        this.trigEl.observe('click', this.formShow.bind(this));

        this.helperDiv = document.createElement('div');
    },

    initializeElement: function(el) {
        el.addClassName('translate-inline');
        Event.observe(el, 'mouseover', this.trigShow.bind(this, el));
        Event.observe(el, 'mouseout', this.trigHideDelayed.bind(this));
    },

    trigShow: function (el) {
        this.trigHideClear();

        var p = Position.cumulativeOffset(el);

        this.trigEl.style.left = p[0]+'px';
        this.trigEl.style.top = p[1]+'px';
        this.trigEl.style.display = 'block';

        this.trigContentEl = el;
    },

    trigHide: function() {
        this.trigEl.style.display = 'none';
        this.trigContentEl = null;
    },

    trigHideDelayed: function () {
        this.trigTimer = window.setTimeout(this.trigHide.bind(this), 500);
    },

    trigHideClear: function() {
        clearInterval(this.trigTimer);
    },

    formShow: function () {
        var el = this.trigContentEl;
        if (!el) {
            return;
        }

        eval('var data = '+el.getAttribute('translate'));

        var content = '<form id="translate-inline-form"><table cellspacing="0">';
        var t = new Template(
            '<tr><td class="label">Scope: </td><td class="value">#{scope}</td></tr>'+
            '<tr><td class="label">Shown: </td><td class="value">#{shown_escape}</td></tr>'+
            '<tr><td class="label">Original: </td><td class="value">#{original_escape}</td></tr>'+
            '<tr><td class="label">Translated: </td><td class="value">#{translated_escape}</td></tr>'+
            '<tr><td class="label"><label for="perstore_#{i}">Store Specific:</label> </td><td class="value">'+
                '<input id="perstore_#{i}" name="translate[#{i}][perstore]" type="checkbox" value="1"/>'+
            '</td></tr>'+
            '<tr><td class="label"><label for="custom_#{i}">Custom:</label> </td><td class="value">'+
                '<input name="translate[#{i}][original]" type="hidden" value="#{scope}::#{original_escape}"/>'+
                '<input id="custom_#{i}" name="translate[#{i}][custom]" class="input-text" value="#{translated_escape}"/>'+
            '</td></tr>'+
            '<tr><td colspan="2"><hr/></td></tr>'
        );
        for (i=0; i<data.length; i++) {
            data[i]['i'] = i;
            data[i]['shown_escape'] = this.escapeHTML(data[i]['shown']);
            data[i]['translated_escape'] = this.escapeHTML(data[i]['translated']);
            data[i]['original_escape'] = this.escapeHTML(data[i]['original']);
            content += t.evaluate(data[i]);
        }
        content += '</table></form>';

        Dialog.confirm(content, {
            draggable:true,
            resizable:true,
            closable:true,
            className:"alphacube",
            title:"Translation",
            width:500,
            height:400,
            //recenterAuto:false,
            hideEffect:Element.hide,
            showEffect:Element.show,
            id:"translate-inline",
            buttonClass:"form-button",
            okLabel:"Submit",
            ok: this.formOk.bind(this)
        });
    },

    formOk: function(win) {
        var inputs = $('translate-inline-form').getInputs(), parameters = [];
        for (var i=0; i<inputs.length; i++) {
            parameters[inputs[i].name] = inputs[i].value;
        }
        new Ajax.Request(this.ajaxUrl, {
            method:'post',
            parameters:parameters
        });
        win.close();
    },

    escapeHTML: function (str) {
       this.helperDiv.innerHTML = '';
       var text = document.createTextNode(str);
       this.helperDiv.appendChild(text);
       var escaped = this.helperDiv.innerHTML;
       escaped = escaped.replace(/"/g, '&quot;');
       return escaped;
    }
}
