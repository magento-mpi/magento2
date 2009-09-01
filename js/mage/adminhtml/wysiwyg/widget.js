/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

var WysiwygWidget = {};
WysiwygWidget.Widget = Class.create();
WysiwygWidget.Widget.prototype = {

    initialize: function(formEl, widgetEl, widgetOptionsEl, optionsSourceUrl) {
        $(formEl).insert({bottom: this.getDivHtml(widgetOptionsEl)});
        this.widgetEl = $(widgetEl);
        this.widgetOptionsEl = $(widgetOptionsEl);
        this.optionsUrl = optionsSourceUrl;
        this.optionValues = new Hash({});

        Event.observe(this.widgetEl, "change", this.loadOptions.bind(this));

        this.initOptionValues();
    },

    getDivHtml: function(id, html) {
        if (!html) html = '';
        return '<div id="' + id + '">' + html + '</div>';
    },

    getOptionsContainerId: function() {
        return this.widgetOptionsEl.id + Base64.idEncode(this.widgetEl.value);
    },

    switchOptionsContainer: function(containerId) {
        $$('#' + this.widgetOptionsEl.id + ' div[id^=' + this.widgetOptionsEl.id + ']').each(function(e) {
            this.disableOptionsContainer(e.id);
        }.bind(this));
        if(containerId != undefined) {
            this.enableOptionsContainer(containerId);
        }
    },

    enableOptionsContainer: function(containerId) {
        $$('#' + containerId + ' .widget-option').each(function(e) {
            e.removeClassName('skip-submit');
            if (e.hasClassName('obligatory')) {
                e.removeClassName('obligatory');
                e.addClassName('required-entry');
            }
        });
        $(containerId).removeClassName('no-display');
    },

    disableOptionsContainer: function(containerId) {
        if ($(containerId).hasClassName('no-display')) {
            return;
        }
        $$('#' + containerId + ' .widget-option').each(function(e) {
            // Avoid submitting fields of unactive container
            if (!e.hasClassName('skip-submit')) {
                e.addClassName('skip-submit');
            }
            // Form validation workaround for unactive container
            if (e.hasClassName('required-entry')) {
                e.removeClassName('required-entry');
                e.addClassName('obligatory');
            }
        });
        $(containerId).addClassName('no-display');
    },

    // Assign widget options values when existing widget selected in WYSIWYG
    initOptionValues: function() {

        if (!this.wysiwygExists()) {
            return false;
        }

        var e = this.getWysiwygNode();
        if (e != undefined && e.id) {
            var widgetCode = Base64.idDecode(e.id);
            this.optionValues = new Hash({});
            widgetCode.gsub(/([a-z0-9\_]+)\s*\=\s*[\"]{1}([^\"]+)[\"]{1}/i, function(match){
                if (match[1] == 'type') {
                    this.widgetEl.value = match[2];
                } else {
                    this.optionValues.set(match[1], match[2]);
                }
            }.bind(this));

            this.loadOptions();
        }
    },

    loadOptions: function() {
        if (!this.widgetEl.value) {
            this.switchOptionsContainer();
            return;
        }

        if ($(this.getOptionsContainerId()) != undefined) {
            this.switchOptionsContainer(this.getOptionsContainerId());
            return;
        }

        this._showWidgetDescription();

        var params = {widget_type: this.widgetEl.value, values: this.optionValues};
        new Ajax.Request(this.optionsUrl,
            {
                parameters: {widget: Object.toJSON(params)},
                onSuccess: function(transport) {
                    try {
                        this.onAjaxSuccess(transport);
                        this.switchOptionsContainer();
                        this.widgetOptionsEl.insert({bottom: this.getDivHtml(this.getOptionsContainerId(), transport.responseText)});
                    } catch(e) {
                        alert(e.message);
                    }
                }.bind(this)
            }
        );
    },

    _showWidgetDescription: function() {
        var noteCnt = this.widgetEl.up().next().down('small');
        var descrCnt = $('widget-description-' + this.widgetEl.selectedIndex);
        if(noteCnt != undefined) {
            var description = (descrCnt != undefined ? descrCnt.innerHTML : '');
            noteCnt.update(descrCnt.innerHTML);
        }
    },

    insertWidget: function() {
        if(editForm.validator && editForm.validator.validate() || !editForm.validator){
            var formElements = [];
            var i = 0;
            $(editForm.formId).getElements().each(function(e) {
                if(!e.hasClassName('skip-submit')) {
                    formElements[i] = e;
                    i++;
                }
            });

            // Add as_is flag to parameters if wysiwyg editor doesn't exist
            var params = Form.serializeElements(formElements);
            if (!this.wysiwygExists()) {
                params = params + '&as_is=1';
            }

            new Ajax.Request($(editForm.formId).readAttribute("action"),
            {
                parameters: params,
                onComplete: function(transport) {
                    try {
                        this.onAjaxSuccess(transport);
                        this.updateContent(transport.responseText);
                        this.getPopup().close();
                    } catch(e) {
                        alert(e.message);
                    }
                }.bind(this)
            });
        }
    },

    updateContent: function(content) {
        if (this.wysiwygExists()) {
        	this.getPopup().execCommand("mceInsertContent", false, content);
        	// Refocus in window
        	if (this.getPopup().isWindow) {
        		window.focus();
        	}
        	this.getWysiwyg().focus();
        } else {
            var parent = this.getPopup().opener;
            var textarea = parent.document.getElementById(this.getPopup().name);
            this.updateElementAtCursor(textarea, content, this.getPopup().opener);
        }
    },


    onAjaxSuccess: function(transport) {
        if (transport.responseText.isJSON()) {
            var response = transport.responseText.evalJSON()
            if (response.error) {
                throw response;
            } else if (response.ajaxExpired && response.ajaxRedirect) {
                setLocation(response.ajaxRedirect);
            }
        }
    },

    wysiwygExists: function() {
        return (typeof tinyMCEPopup != 'undefined') && (typeof tinyMCEPopup.editor != 'undefined');
    },

    getPopup: function() {
        if (this.wysiwygExists()) {
            return tinyMCEPopup;
        } else {
            return window.self;
        }
    },

    getWysiwyg: function() {
        return tinyMCEPopup.editor;
    },

    getWysiwygNode: function() {
        return tinyMCEPopup.editor.selection.getNode();
    },

    // Insert some content to the cursor position of input element
    updateElementAtCursor: function(el, value, win) {
        if (document.selection) {
            el.focus();
            sel = win.document.selection.createRange();
            sel.text = value;
        } else if (el.selectionStart || el.selectionStart == '0') {
            var startPos = el.selectionStart;
            var endPos = el.selectionEnd;
            el.value = el.value.substring(0, startPos) + value + el.value.substring(endPos, el.value.length);
        } else {
            el.value += value;
        }
    }
}

WysiwygWidget.chooser = Class.create();
WysiwygWidget.chooser.prototype = {

    // HTML element A, on which click event fired when choose a selection
    chooserId: null,

    // Source URL for Ajax requests
    chooserUrl: null,

    initialize: function(chooserId, chooserUrl) {
        this.chooserId = chooserId;
        this.chooserUrl = chooserUrl;
    },

    choose: function(event) {
        //var element = Event.findElement(event, 'A');
        var chooser = $(this.chooserId);
        var responseContainerId = "responseCnt" + this.chooserId;
        if ($(responseContainerId) != undefined) {
            if ($(responseContainerId).visible()) {
                $(responseContainerId).hide();
            } else {
                $(responseContainerId).show();
            }
            return;
        }
        new Ajax.Request(this.chooserUrl,
            {
                parameters: {},
                onSuccess: function(transport) {
                    try {
                        wWidget.onAjaxSuccess(transport);
                        chooser.next("label.widget-option-label").insert({after: wWidget.getDivHtml(responseContainerId, transport.responseText)});
                    } catch(e) {
                        alert(e.message);
                    }
                }.bind(this)
            }
        );
    }
}
