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

    initialize: function(formId, optionsSourceUrl) {
        $(formId).insert({bottom: this.getDivHtml('widget_options')});
        this.widgetCodeEl = $("select_widget_code");
        this.widgetOptionsEl = $("widget_options");
        this.optionsUrl = optionsSourceUrl;
        this.optionValues = new Hash({});

        Event.observe(this.widgetCodeEl, "change", this.loadOptions.bind(this));

        this.initOptionValues();
    },

    getDivHtml: function(id, html) {
        if (!html) html = '';
        return '<div id="' + id + '">' + html + '</div>';
    },

    getOptionsContainerId: function() {
        return this.widgetOptionsEl.id + this.widgetCodeEl.value;
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
        var ed = tinyMCEPopup.editor;
        var e = ed.selection.getNode();

        if (e != undefined && e.id && ed.dom.getAttrib(e, 'class').indexOf('widget') != -1) {
            var widgetId = e.id.split("-");
            if (widgetId.length == 2) {
                var code = widgetId[0];
                var widgetCode = Base64.mageDecode(widgetId[1]);
                this.widgetCodeEl.value = code;
                this.optionValues = new Hash({});

                widgetCode.gsub(/([a-z0-9\_]+)\s*\=\s*[\"]{1}([^\"]+)[\"]{1}/i, function(match){
                    this.optionValues.set(match[1], match[2]);
                }.bind(this));

                this.loadOptions();
            }
        }
    },

    loadOptions: function() {
        if (!this.widgetCodeEl.value) {
            this.switchOptionsContainer();
            return;
        }

        if ($(this.getOptionsContainerId()) != undefined) {
            this.switchOptionsContainer(this.getOptionsContainerId());
            return;
        }

        this._showWidgetDescription();

        var params = {widget_code: this.widgetCodeEl.value, values: this.optionValues};
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
        var noteCnt = this.widgetCodeEl.up().next().down('small');
        var descrCnt = $(this.widgetCodeEl.value + '-description');
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

            new Ajax.Request($(editForm.formId).readAttribute("action"),
            {
                parameters: Form.serializeElements( formElements ),
                onComplete: function(transport) {
                    try {
                        this.onAjaxSuccess(transport);
                    	tinyMCEPopup.execCommand("mceInsertContent", false, transport.responseText);
                    	// Refocus in window
                    	if (tinyMCEPopup.isWindow) {
                    		window.focus();
                    	}
                    	tinyMCEPopup.editor.focus();
                    	tinyMCEPopup.close(); // cannot directly call TinyMCEPopup.close() in Prototype class scope
                    } catch(e) {
                        alert(e.message);
                    }
                }.bind(this)
            });
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
