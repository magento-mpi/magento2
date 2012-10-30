/**
 * {license_notice}
 *
 * @category    mage install
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true */
(function($) {
    $.extend(true, $.mage, {
        install: {
            /**
             * Beginning phase of the installation process. Check the box to agree to Terms and
             * Conditions, License, etc. and then click the Continue button.
             */
            begin: function(agreeBox, submitButton) {
                $(agreeBox).on('click', function(e) {
                    var btn = $(submitButton);
                    if (e.target.checked) {
                        btn.removeClass('mage-disabled').addClass('mage-enabled')
                            .removeAttr('disabled');
                    } else {
                        btn.removeClass('mage-enabled').addClass('mage-disabled')
                            .attr('disabled', 'disabled');
                    }
                });
            },

            /**
             * Configuration phase. Prompt for hostname, database information, and options,
             * such as whether to enable SSL, referred to as secure options.
             */
            configureForm: function(form, useSecure, useSecureOptions) {
                $(form).mage().validate();
                $(useSecure).on('click', function(e) {
                    return e.target.checked ?
                        $(useSecureOptions).show() : $(useSecureOptions).hide();
                });
            },
            configureContinue: function(continueButton, url) {
                $(continueButton).on('click', function() {
                    location.href = url;
                });
            },

            /**
             * Create backend administrator login form validation. Enter user's name, email,
             * admin username, and password. Validate the form.
             */
            createAdmin: function(form) {
                $(form).mage().validate({errorClass: 'mage-error', errorElement: 'div'});
            },

            /**
             * Generate a new URL whenever a different locale is selected and refresh the
             * page to that new locale based URL.
             */
            changeLocale: function(localeField, url) {
                $(localeField).on('change', function() {
                    location.href = url + 'locale/' + $(localeField).val() + '/?timezone=' +
                        $('#timezone').val() + '&amp;currency=' + $('#currency').val();
                });
            }
        }
    });
})(jQuery);
