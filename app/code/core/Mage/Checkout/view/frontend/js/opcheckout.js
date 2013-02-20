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
            loginGuestSelector: '#login\\:guest',
            loginRegisterSelector: '#login\\:register',
            continueSelector: '#onepage-guest-register-button',
            registerCustomerPasswordSelector: '#register-customer-password',
            sectionSelectorPrefix: '#opc-',
            billingSection: 'billing',
            ajaxLoaderPlaceButton: false,
            updateSelectorPrefix: '#checkout-',
            updateSelectorSuffix: '-load'
        },

        _create: function() {
            var _this = this;
            this.element
                .on('click', this.options.continueSelector, function() {
                    $.proxy(_this._continue($(this)), _this);
                })
                .on('gotoSection', function(event, section) {
                    $.proxy(_this._ajaxUpdateProgress(section), _this);
                    _this.element.trigger('enableSection', {selector: _this.options.sectionSelectorPrefix + section});
                })
                .on('ajaxError', $.proxy(this._ajaxError, this))
                .on('ajaxSend', $.proxy(this._ajaxSend, this))
                .on('ajaxComplete', $.proxy(this._ajaxComplete, this));
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
                if ($(this.options.loginGuestSelector).is(':checked')) {
                    this._ajaxContinue(this.options.saveMethodUrl, {method:'guest'}, this.options.billingSection);
                    this.element.find(this.options.registerCustomerPasswordSelector).hide();
                } else if ($(this.options.loginRegisterSelector).is(':checked')) {
                    this._ajaxContinue(this.options.saveMethodUrl, {method:'register'}, this.options.billingSection);
                    this.element.find(this.options.registerCustomerPasswordSelector).show();
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
                success: function(response) {
                    if (successCallback) {
                        successCallback(response);
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
                            $(this.options.updateSelectorPrefix + response.update_section.name + this.options.updateSelectorSuffix).html(response.update_section.html);
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
            billingAddressDropdownSelector: '#billing-address-select',
            billingNewAddressFormSelector: '#billing-new-address-form',
            billingContinueSelector: '#billing-buttons-container .button',
            billingForm: '#co-billing-form'
        },

        _create: function() {
            this._super();
            this.element
                .on('change', this.options.billingAddressDropdownSelector, $.proxy(function(e) {
                    this.element.find(this.options.billingNewAddressFormSelector).toggle(!$(e.target).val());
                }, this))
                .on('click', this.options.billingContinueSelector, $.proxy(function() {
                    this._ajaxContinue(this.options.saveBillingUrl, $(this.options.billingForm).serialize());
                }, this));
        }
    });

    // Extension for mage.opcheckout - third section(Shipping Information) in one page checkout accordion
    $.widget('mage.opcheckout', $.mage.opcheckout, {
        options: {
            shippingForm: '#co-shipping-form',
            shippingAddressDropdownSelector: '#shipping-address-select',
            shippingNewAddressFormSelector: '#shipping-new-address-form',
            billingCopySelector: '#shipping\\:same_as_billing',
            countrySelector: '#shipping\\:country_id',
            shippingContinueSelector:'#shipping-buttons-container .button'
        },

        _create: function() {
            this._super();
            this.element
                .on('change', this.options.shippingAddressDropdownSelector, $.proxy(function(e) {
                    $(this.options.shippingNewAddressFormSelector).toggle(!$(e.target).val());
                }, this))
                .on('input propertychange', this.options.shippingForm + ' :input[name]', $.proxy(function() {
                    $(this.options.billingCopySelector).prop('checked', false);
                }, this))
                .on('click', this.options.billingCopySelector, $.proxy(function(e) {
                    if ($(e.target).is(':checked')) {
                        this._billingToShipping();
                    }
                }, this))
                .on('click', this.options.shippingContinueSelector, $.proxy(function() {
                    this._ajaxContinue(this.options.saveShippingUrl, $(this.options.shippingForm).serialize());
                }, this));
        },

        /**
         * Copy billing address info to shipping address
         * @private
         */
        _billingToShipping: function() {
            $(':input[name]', this.options.billingForm).each($.proxy(function(key, value) {
                var jQfieldObj = $(value.id.replace('billing:', '#shipping\\:'));
                jQfieldObj.val($(value).val());
                if (jQfieldObj.is("select")) {
                    jQfieldObj.trigger('change');
                }
            }, this));
            $(this.options.billingCopySelector).prop('checked', true);
        }
    });
})(jQuery, window);