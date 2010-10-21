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
 * @package     js
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
/**
 * Disable cart server validation in admin
 */
AdminOrder.prototype.switchPaymentMethod = function(method){
        this.setPaymentMethod(method);
        if (method != 'directpayment') {
	        var data = {};
	        data['order[payment_method]'] = method;
	        this.loadArea(['card_validation'], true, data);
        }
};
AdminOrder.prototype.setPaymentMethod = function(method){
    if (this.paymentMethod && $('payment_form_'+this.paymentMethod)) {
        var form = $('payment_form_'+this.paymentMethod);
        form.hide();
        var elements = form.select('input', 'select');
        for (var i=0; i<elements.length; i++) elements[i].disabled = true;
    }

    if(!this.paymentMethod || method){
        $('order-billing_method_form').select('input', 'select').each(function(elem){
            if(elem.type != 'radio') elem.disabled = true;
        })
    }

    if ($('payment_form_'+method)){
        this.paymentMethod = method;
        var form = $('payment_form_'+method);
        form.show();
        var elements = form.select('input', 'select');
        for (var i=0; i<elements.length; i++) {
            elements[i].disabled = false;
            if(!elements[i].bindChange && method != 'directpayment'){
                elements[i].bindChange = true;
                elements[i].paymentContainer = 'payment_form_'+method; //@deprecated after 1.4.0.0-rc1
                elements[i].method = method;
                elements[i].observe('change', this.changePaymentData.bind(this))
            }
        }
    }
};