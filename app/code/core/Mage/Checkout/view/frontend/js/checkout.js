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
    $.widget('mage.checkout', {
        options: {
            loginGuestSelector: '#login\\:guest',
            loginRegisterSelector: '#login\\:register',
            continueSelector: '#onepage-guest-register-button',
            registerCustomerPasswordSelector: '#register-customer-password',
            sectionSelectorPrefix: '#opc-',
            nextSection: 'billing',
            ajaxLoaderPlaceButton: false
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
                    this._ajaxCheckoutSave('guest');
                } else if ($(this.options.loginRegisterSelector).is(':checked')) {
                    this._ajaxCheckoutSave('register');
                } else {
                    alert($.mage.__('Please choose to register or to checkout as a guest'));
                }
            }
            return false;
        },

        /**
         * Ajax call to save checkout info to backend and enable next section in accordion
         * @private
         * @param method - checkout method guest or register
         */
        _ajaxCheckoutSave: function(method) {
            $.ajax({
                url: this.options.saveMethodUrl,
                type: 'post',
                cache: false,
                context: this,
                data: {method: method},
                success: function() {
                    if (method === 'guest') {
                        $(this.options.registerCustomerPasswordSelector).hide();
                    }
                    if (method === 'register') {
                        $(this.options.registerCustomerPasswordSelector).show();
                    }
                    this.element.trigger('gotoSection', this.options.nextSection);
                    $(document).trigger('login:setMethod', {method: method});
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
})(jQuery, window);
