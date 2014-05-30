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
(function($, window) {
    'use strict';    
    // Extension for mage.opcheckout - last section(Order Review) in one page checkout accordion
    $.widget('mage.opcOrderReview', $.mage.opcPaymentInfo, {
        options: {
            review: {
                continueSelector: '#opc-review [data-role=review-save]',
                container: '#opc-review',
                agreementFormSelector: '#checkout-agreements-form'
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
            var agreementForm = $(this.options.review.agreementFormSelector),
                paymentForm = $(this.options.payment.form);
            agreementForm.validation();
            if (agreementForm.validation &&
                agreementForm.validation('isValid') &&
                paymentForm.validation &&
                paymentForm.validation('isValid')) {
                this._ajaxContinue(
                    this.options.review.saveUrl,
                    paymentForm.serialize() + '&' + agreementForm.serialize());
            }
        }
    });
})(jQuery, window);
