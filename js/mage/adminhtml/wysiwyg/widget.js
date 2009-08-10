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
        new Insertion.Bottom(formId, this.getDivHtml('widget_options'));

        this.widgetCodeEl = $("select_widget_code");
        this.widgetOptionsEl = $("widget_options");
        this.optionsUrl = optionsSourceUrl;

        Event.observe(this.widgetCodeEl, "change", this.loadOptions.bind(this));
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

    loadOptions: function() {
        if (!this.widgetCodeEl.value) {
            this.switchOptionsContainer();
            return;
        }

        if ($(this.getOptionsContainerId()) != undefined) {
            this.switchOptionsContainer(this.getOptionsContainerId());
            return;
        }

        new Ajax.Request(this.optionsUrl,
            {
                parameters:{widget_code: this.widgetCodeEl.value},
                onSuccess: function(transport) {
                    try {
                        this.onAjaxSuccess(transport);
                        this.switchOptionsContainer();
                        new Insertion.Bottom(this.widgetOptionsEl, this.getDivHtml(this.getOptionsContainerId(), transport.responseText));
                    } catch(e) {
                        alert(e.message);
                    }
                }.bind(this)
            }
        );
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
                    	tinyMCEPopup.close();
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
