/**
 * {license_notice}
 *
 * @category    one page checkout last step
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define([
    "jquery",
    "jquery/ui",
    "Magento_Checkout/js/opc-payment-info"
], function($){
    'use strict';

    // Extension for mage.opcheckout - last section(Order Review) in one page checkout accordion
    $.widget('mage.opcOrderReview', $.mage.opcPaymentInfo, {
        options: {
            review: {
                continueSelector: '#opc-review [data-role=review-save]',
                container: '#opc-review',
                agreementGroupSelector: '#checkout-agreements'
            }
        },

        _create: function() {
            this._super();
            var events = {};
            events['click ' + this.options.review.continueSelector] = this._saveOrder;
            events['saveOrder' + this.options.review.container] = this._saveOrder;
            this._on(events);
        },

        _saveOrder: function() {
            var agreementGroupSelector = $(this.options.review.agreementGroupSelector),
                paymentForm = $(this.options.payment.form);
            var isAgreementValid = true;
            agreementGroupSelector.find('form').each(
                function(){
                    $(this).validation();
                    isAgreementValid = isAgreementValid && $(this).validation && $(this).validation('isValid');
                }
            );

            if (isAgreementValid &&
                paymentForm.validation &&
                paymentForm.validation('isValid')) {
                var serializedAgreement = '';
                agreementGroupSelector.find('form').each(function(){serializedAgreement += '&' + $(this).serialize()});
                this._ajaxContinue(
                    this.options.review.saveUrl,
                    paymentForm.serialize() + serializedAgreement);
            }
        }
    });

});
