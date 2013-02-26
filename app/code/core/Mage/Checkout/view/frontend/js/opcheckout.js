/**
 * {license_notice}
 *
 * @category    one page checkout first step
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
/*global alert*/
(function($, window) {
    'use strict';
    // Base widget, handle ajax events and first section(Checkout Method) in one page checkout accordion
    $.widget('mage.opcheckout', {
        options: {
            checkout: {
                loginGuestSelector: '#login\\:guest',
                loginRegisterSelector: '#login\\:register',
                continueSelector: '#onepage-guest-register-button',
                registerCustomerPasswordSelector: '#register-customer-password'
            },
            sectionSelectorPrefix: '#opc-',
            billingSection: 'billing',
            ajaxLoaderPlaceButton: false,
            updateSelectorPrefix: '#checkout-',
            updateSelectorSuffix: '-load',
            backSelector: '.back-link'
        },

        _create: function() {
            var _this = this;
            this.element
                .on('click', this.options.checkout.continueSelector, function() {
                    $.proxy(_this._continue($(this)), _this);
                })
                .on('gotoSection', function(event, section) {
                    $.proxy(_this._ajaxUpdateProgress(section), _this);
                    _this.element.trigger('enableSection', {selector: _this.options.sectionSelectorPrefix + section});
                })
                .on('ajaxError', $.proxy(this._ajaxError, this))
                .on('click', this.options.backSelector, function() {
                    _this.element.trigger('enableSection', {selector: '#' + _this.element.find('.active').prev().attr('id')});
                });
            $(this.options.checkoutProgressContainer).on('click', '[data-goto-section]', $.proxy(function(e) {
                var gotoSection = $(e.target).data('goto-section');
                this._ajaxUpdateProgress(gotoSection);
                this.element.trigger('enableSection', {selector: _this.options.sectionSelectorPrefix + gotoSection});
                return false;
            }, this));
        },

        /**
         * Callback function for before ajax send event(global)
         * @private
         */
        _ajaxSend: function() {
            var loader = this.element.find('.section.active .please-wait').show();
            if (this.options.ajaxLoaderPlaceButton) {
                loader.siblings('.button').hide();
            }
        },

        /**
         * Callback function for ajax complete event(global)
         * @private
         */
        _ajaxComplete: function() {
            this.element.find('.please-wait').hide();
            if (this.options.ajaxLoaderPlaceButton) {
                this.element.find('.button').show();
            }
        },

        /**
         * ajax error for all onepage checkout ajax calls
         * @private
         */
        _ajaxError: function() {
            window.location.href = this.options.failureUrl;
        },

        /**
         * callback function when continue button is clicked
         * @private
         * @param elem - continue button
         * @return {Boolean}
         */
        _continue: function(elem) {
            var json = elem.data('checkout');
            if (json.isGuestCheckoutAllowed) {
                if ($(this.options.checkout.loginGuestSelector).is(':checked')) {
                    this._ajaxContinue(this.options.checkout.saveUrl, {method:'guest'}, this.options.billingSection);
                    this.element.find(this.options.checkout.registerCustomerPasswordSelector).hide();
                } else if ($(this.options.checkout.loginRegisterSelector).is(':checked')) {
                    this._ajaxContinue(this.options.checkout.saveUrl, {method:'register'}, this.options.billingSection);
                    this.element.find(this.options.checkout.registerCustomerPasswordSelector).show();
                } else {
                    alert($.mage.__('Please choose to register or to checkout as a guest'));
                }
            }
            return false;
        },

        /**
         * Ajax call to save checkout info to backend and enable next section in accordion
         * @private
         * @param url - ajax url
         * @param data - post data for ajax call
         * @param gotoSection - the section needs to show after ajax call
         * @param successCallback - custom callback function in ajax success
         */
        _ajaxContinue: function(url, data, gotoSection, successCallback) {
            $.ajax({
                url: url,
                type: 'post',
                context: this,
                data: data,
                dataType: 'json',
                beforeSend: this._ajaxSend,
                complete: this._ajaxComplete,
                success: function(response) {
                    if (successCallback) {
                        successCallback.call(this, response);
                    }
                    if ($.type(response) === 'object' && !$.isEmptyObject(response)) {
                        if (response.error) {
                            var msg = response.message;
                            if (msg) {
                                if ($.type(msg) === 'array') {
                                    msg = msg.join("\n");
                                }
                                $(this.options.countrySelector).trigger('change');
                                alert($.mage.__(msg));
                            }
                        }
                        if (response.update_section) {
                            $(this.options.updateSelectorPrefix + response.update_section.name + this.options.updateSelectorSuffix)
                                .html($(response.update_section.html)).trigger('contentUpdated');
                        }
                        if (response.allow_sections) {
                            response.allow_sections.each(function() {
                                $(this).addClass('allow');
                            });
                        }
                        if (response.duplicateBillingInfo) {
                            $(this.options.billingCopySelector).prop('checked', true).trigger('click');
                        }
                        if (response.redirect) {
                            $.mage.redirect(response.redirect);
                        }
                        if (response.success) {
                            $.mage.redirect(this.options.review.successUrl);
                        }
                        if (response.goto_section) {
                            this.element.trigger('gotoSection', response.goto_section);
                        }
                    } else {
                        this.element.trigger('gotoSection', gotoSection);
                    }
                }
            });
        },

        /**
         * Update progress sidebar content
         * @private
         * @param toStep
         */
        _ajaxUpdateProgress: function(toStep) {
            $.ajax({
                url: this.options.progressUrl,
                type: 'get',
                cache: false,
                context: this,
                data: toStep ? {toStep: toStep} : null,
                success: function(response) {
                    $(this.options.checkoutProgressContainer).html(response);
                }
            });
        }
    });

    // Extension for mage.opcheckout - second section(Billing Information) in one page checkout accordion
    $.widget('mage.opcheckout', $.mage.opcheckout, {
        options: {
            billing: {
                addressDropdownSelector: '#billing-address-select',
                newAddressFormSelector: '#billing-new-address-form',
                continueSelector: '#billing-buttons-container .button',
                form: '#co-billing-form'
            }
        },

        _create: function() {
            this._super();
            this.element
                .on('change', this.options.billing.addressDropdownSelector, $.proxy(function(e) {
                    this.element.find(this.options.billing.newAddressFormSelector).toggle(!$(e.target).val());
                }, this))
                .on('click', this.options.billing.continueSelector, $.proxy(function() {
                    if ($(this.options.billing.form).validation && $(this.options.billing.form).validation('isValid')) {
                    this._ajaxContinue(this.options.billing.saveUrl, $(this.options.billing.form).serialize(), false, function() {
                        //Trigger indicating billing save. eg. GiftMessage listens to this to inject gift options
                        this.element.trigger('billingSave');
                    });
                    }
                }, this))
                .find(this.options.billing.form).validation();
        }
    });

    // Extension for mage.opcheckout - third section(Shipping Information) in one page checkout accordion
    $.widget('mage.opcheckout', $.mage.opcheckout, {
        options: {
            shipping: {
                form: '#co-shipping-form',
                addressDropdownSelector: '#shipping-address-select',
                newAddressFormSelector: '#shipping-new-address-form',
                copyBillingSelector: '#shipping\\:same_as_billing',
                countrySelector: '#shipping\\:country_id',
                continueSelector:'#shipping-buttons-container .button'
            }
        },

        _create: function() {
            this._super();
            this.element
                .on('change', this.options.shipping.addressDropdownSelector, $.proxy(function(e) {
                    $(this.options.shipping.newAddressFormSelector).toggle(!$(e.target).val());
                }, this))
                .on('input propertychange', this.options.shipping.form + ' :input[name]', $.proxy(function() {
                    $(this.options.shipping.copyBillingSelector).prop('checked', false);
                }, this))
                .on('click', this.options.copyBillingSelector, $.proxy(function(e) {
                    if ($(e.target).is(':checked')) {
                        this._billingToShipping();
                    }
                }, this))
                .on('click', this.options.shipping.continueSelector, $.proxy(function() {
                    if ($(this.options.shipping.form).validation && $(this.options.shipping.form).validation('isValid')) {
                    this._ajaxContinue(this.options.shipping.saveUrl, $(this.options.shipping.form).serialize(), false, function() {
                        //Trigger indicating shipping save. eg. GiftMessage listens to this to inject gift options
                        this.element.trigger('shippingSave');
                    });
                    }
                }, this))
                .find(this.options.shipping.form).validation();
        },

        /**
         * Copy billing address info to shipping address
         * @private
         */
        _billingToShipping: function() {
            $(':input[name]', this.options.billing.form).each($.proxy(function(key, value) {
                var fieldObj = $(value.id.replace('billing:', '#shipping\\:'));
                fieldObj.val($(value).val());
                if (fieldObj.is("select")) {
                    fieldObj.trigger('change');
                }
            }, this));
            $(this.options.shipping.copyBillingSelector).prop('checked', true);
        }
    });

    // Extension for mage.opcheckout - fourth section(Shipping Method) in one page checkout accordion
    $.widget('mage.opcheckout', $.mage.opcheckout, {
        options: {
            shippingMethod: {
                continueSelector: '#shipping-method-buttons-container .button',
                form: '#co-shipping-method-form'
            }
        },

        _create: function() {
            this._super();
            this.element
                .on('click', this.options.shippingMethod.continueSelector, $.proxy(function() {
                    if (this._validateShippingMethod()&&
                        $(this.options.shippingMethod.form).validation &&
                        $(this.options.shippingMethod.form).validation('isValid')) {
                        this._ajaxContinue(this.options.shippingMethod.saveUrl, $(this.options.shippingMethod.form).serialize());
                    }
                }, this))
                .find(this.options.shippingMethod.form).validation();
        },

        /**
         * Make sure at least one shipping method is selected
         * @return {Boolean}
         * @private
         */
        _validateShippingMethod: function() {
            var methods = this.element.find('[name="shipping_method"]');
            if (methods.length === 0) {
                alert($.mage.__('Your order cannot be completed at this time as there is no shipping methods available for it. Please make necessary changes in your shipping address.'));
                return false;
            }
            if (methods.filter(':checked').length) {
                return true;
            }
            alert($.mage.__('Please specify shipping method.'));
            return false;
        }
    });

    // Extension for mage.opcheckout - fifth section(Payment Information) in one page checkout accordion
    $.widget('mage.opcheckout', $.mage.opcheckout, {
        options: {
            payment: {
                continueSelector: '#payment-buttons-container .button',
                form: '#co-payment-form'
            }
        },

        _create: function() {
            this._super();
            this.element
                .on('click', this.options.payment.continueSelector, $.proxy(function() {
                    if (this._validatePaymentMethod() &&
                        $(this.options.payment.form).validation &&
                        $(this.options.payment.form).validation('isValid')) {
                        this._ajaxContinue(this.options.payment.saveUrl, $(this.options.payment.form).serialize());
                    }
                }, this))
                .find(this.options.payment.form).validation();
            this.element.on('click', this.options.payment.form + ' dt input:radio', $.proxy(this._paymentMethodHandler, this));
        },

        _paymentMethodHandler: function(e) {
            var _this = $(e.target),
                parentsDl = _this.closest('dl');
            parentsDl.find('dt input:radio').prop('checked', false);
            _this.prop('checked', true);
            parentsDl.find('dd ul').hide();
            _this.parent().nextUntil('dt').find('ul').show();
        },

        _validatePaymentMethod: function() {
            var methods = this.element.find('[name="payment[method]"]');
            if (methods.length === 0) {
                alert($.mage.__('Your order cannot be completed at this time as there is no payment methods available for it.'));
                return false;
            }
            if (methods.filter(':checked').length) {
                return true;
            }
            alert($.mage.__('Please specify payment method.'));
            return false;
        }
    });

    // Extension for mage.opcheckout - last section(Order Review) in one page checkout accordion
    $.widget('mage.opcheckout', $.mage.opcheckout, {
        options: {
            review: {
                continueSelector: '#review-buttons-container .button'
            }
        },

        _create: function() {
            this._super();
            this.element
                .on('click', this.options.review.continueSelector, $.proxy(function() {
                    if ($(this.options.payment.form).validation &&
                        $(this.options.payment.form).validation('isValid')) {
                        this._ajaxContinue(this.options.review.saveUrl, $(this.options.payment.form).serialize());
                    }
                }, this));
        }
    });
})(jQuery, window);