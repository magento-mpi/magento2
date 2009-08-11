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
WysiwygWidget.optionProduct = Class.create();
WysiwygWidget.optionProduct.prototype = {

    chooserId: null,
    initialize: function(objectName, chooserUrl) {
        this.chooserUrl = chooserUrl;
        this.selfObjectName = objectName;
    },

    choose: function(event) {
        var element = Event.findElement(event, 'A');
        this.chooserId = element.id;
        var responseContainerId = "responseCnt" + element.id;
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
                parameters: {'js_chooser_object':this.selfObjectName},
                onSuccess: function(transport) {
                    try {
                        wWidget.onAjaxSuccess(transport);
                        new Insertion.After(element, wWidget.getDivHtml(responseContainerId, transport.responseText));
                    } catch(e) {
                        alert(e.message);
                    }
                }.bind(this)
            }
        );
    },

    clickProduct: function (grid, event) {
        var trElement = Event.findElement(event, "tr");
        var id = trElement.down("td").innerHTML;
        $(this.chooserId).previous("input").value = "product/" + id;
        var responseContainerId = "responseCnt" + this.chooserId;
        $(responseContainerId).hide();
    }
}
