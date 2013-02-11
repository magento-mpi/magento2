/**
 * {license_notice}
 *
 * @category    mage
 * @package     checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true*/
(function($) {
    "use strict";
    $.widget('mage.onePageLogin', {
        options: {
            loginEmail: '#login-email',
            loginPassword: '#login-password',
            submitButton: '[data-role="one-page-login"]'
        },

        /**
         * Bind keydown event handlers to login form input fields and click event to submit button.
         * @private
         */
        _create: function() {
            $(this.options.loginEmail).keydown($.proxy(this._keyDown, this));
            $(this.options.loginPassword).keydown($.proxy(this._keyDown, this));
            $(this.options.submitButton).on('click', $.proxy(this._submit, this));
        },

        /**
         * Handle keydown event. Submit the form if the enter key is pressed on the input field.
         * @param event {Event} - keydown event
         * @private
         */
        _keyDown: function(event) {
            if (event.which === $.ui.keyCode.ENTER) {
                this.element.submit();
            }
        },

        /**
         * Disable the button and submit the form if it validates without error.
         * @param event {Event} - click event
         * @private
         */
        _submit: function(event) {
            if (this.element.valid()) {
                $(event.target).prop('disabled', true);
                this.element.submit();
            }
        }
    });
})(jQuery);
