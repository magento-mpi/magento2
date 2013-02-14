/**
 * {license_notice}
 *
 * @category    mage checkout
 * @package     review
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true browser:true*/
(function($) {
    'use strict';
    $.widget('mage.review', {
        options: {
            loaderClass: '.please-wait',
            paymentForm: '#co-payment-form',
            agreementsForm: '#checkout-agreements',
            containerSelector: '#checkoutSteps'
        },
        _create: function() {
            this.element.on('click', $.proxy(this._ajaxSave, this));
        },

        /**
         * Ajax call to save payment methods and agreement forms to back end
         * @private
         */
        _ajaxSave: function() {
            if ($(this.options.loaderClass).is(':visible')) {
                return false;
            }
            var params = $(this.options.paymentForm).serialize() + '&' + $(this.options.agreementsForm).serialize();
            $.ajax({
                url: this.options.saveMethodUrl,
                type: 'post',
                context: this,
                dataType: 'json',
                data: params,
                success:this._ajaxSuccess
            });

        },

        /**
         * callback function for successful ajax call
         * @private
         * @param object
         */
        _ajaxSuccess: function(response) {
            if (response.redirect) {
                $.mage.redirect(response.redirect);
            }

            if (response.success) {
                $.mage.redirect(this.options.successUrl);
            }

            if (response.error) {
                var msg = response.error_messages;
                if (msg) {
                    if ($.type(msg) === 'array') {
                        msg = msg.join("\n");
                    }
                    alert($.mage.__(msg));
                }
            }

            if (response.update_section) {
                $('#checkout-' + response.update_section.name + '-load').html(response.update_section.html);
            }

            if (response.goto_section) {
                $(this.options.containerSelector).trigger('enableSection', {selector: 'opc' + response.goto_section})
            }
        }
    });
})(jQuery);
