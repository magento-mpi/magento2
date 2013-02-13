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
            nextSectionSelector: '#opc-billing'
        },

        _create: function() {
            var _this = this;
            this.element.on('click', this.options.continueSelector, function() {
                $.proxy(_this._continue($(this)), _this);
            }).on('ajaxError', $.proxy(this._ajaxError, this));
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
            if (json.isGuessCheckoutAllowed) {
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
                    this.element.trigger('enableSection', {selector: this.options.nextSectionSelector});
                    $(document).trigger('login:setMethod', {method: method});
                }
            });
        }
    });
})(jQuery, window);
