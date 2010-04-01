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
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

Event.observe(window, 'load', function() {
    var pConfig = new PaypalConfig;
    Element.observe('paypal_tmp_general_country', 'change', pConfig.trackPayflowEnabled.bind(pConfig));
    Element.observe('paypal_tmp_general_use_payflow', 'change', pConfig.trackPayflowEnabled.bind(pConfig));
    pConfig.trackPayflowEnabled();
});

PaypalConfig = Class.create();
PaypalConfig.prototype = {
    initialize: function(){

    },
    trackPayflowEnabled: function()
    {
        var usePayflow = $('paypal_tmp_general_country') != undefined
            && $('paypal_tmp_general_country').value == 'GB'
            && $('paypal_tmp_general_use_payflow') != undefined
            && $('paypal_tmp_general_use_payflow').value == '1';
        var payflowRows = [
            'row_paypal_tmp_pro_heading_payflow',
            'row_paypal_tmp_pro_partner',
            'row_paypal_tmp_pro_user',
            'row_paypal_tmp_pro_vendor',
            'row_paypal_tmp_pro_pwd'
        ];
        var notPayflowRows = [
            'row_paypal_tmp_general_heading_api',
            'row_paypal_tmp_general_api_username',
            'row_paypal_tmp_general_api_password',
            'row_paypal_tmp_general_api_signature',
            'row_paypal_tmp_general_use_proxy'
        ];
        if (usePayflow) {
            payflowRows.each(function(e) {
                this.enableRow(e);
            }.bind(this));
            notPayflowRows.each(function(e) {
                this.disableRow(e);
            }.bind(this));
        } else {
            payflowRows.each(function(e) {
                this.disableRow(e);
            }.bind(this));
            notPayflowRows.each(function(e) {
                this.enableRow(e);
            }.bind(this));
        }
    },

    enableRow: function(rowId)
    {
        $(rowId).select('input','select').each(function(e) {
            e.disabled = false;
        });
        $(rowId).show();
    },

    disableRow: function(rowId)
    {
        $(rowId).select('input','select').each(function(e) {
            e.disabled = true;
        });
        $(rowId).hide();
    }
}

