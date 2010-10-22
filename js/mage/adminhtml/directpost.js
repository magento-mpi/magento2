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
AdminOrder.prototype.prepareParams = function(params) {
	if (!params) {
		params = {};
	}
	if (!params.customer_id) {
		params.customer_id = this.customerId;
	}
	if (!params.store_id) {
		params.store_id = this.storeId;
	}
	if (!params.currency_id) {
		params.currency_id = this.currencyId;
	}
	if (!params.form_key) {
		params.form_key = FORM_KEY;
	}

	if (this.paymentMethod != 'authorizenet_directpost') {
		var data = this.serializeData('order-billing_method');
		if (data) {
			data.each(function(value) {
				params[value[0]] = value[1];
			});
		}
	} else {
		params['payment[method]'] = 'authorizenet_directpost';
	}
	return params;
};
AdminOrder.prototype.getPaymentData = function(currentMethod) {
	if (typeof (currentMethod) == 'undefined') {
		if (this.paymentMethod) {
			currentMethod = this.paymentMethod;
		} else {
			return false;
		}
	}
	if (currentMethod == 'authorizenet_directpost') {
		return false;
	}
	var data = {};
	var fields = $('payment_form_' + currentMethod).select('input', 'select');
	for ( var i = 0; i < fields.length; i++) {
		data[fields[i].name] = fields[i].getValue();
	}
	if ((typeof data['payment[cc_type]']) != 'undefined'
			&& (!data['payment[cc_type]'] || !data['payment[cc_number]'])) {
		return false;
	}
	return data;
};
