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
    $.widget('mage.begin', {
        /**
         * Beginning phase of the installation process. Check the box to agree to Terms and
         * Conditions, License, etc. and then click the Continue button.
         */
        _create: function() {
            this.element.on('click', $.proxy(function(e) {
                var btn = $(this.options.submitButtonSelector);
                if (e.target.checked) {
                    btn.removeClass('mage-disabled').addClass('mage-enabled')
                        .removeAttr('disabled');
                } else {
                    btn.removeClass('mage-enabled').addClass('mage-disabled')
                        .attr('disabled', 'disabled');
                }
            }, this));
        }
    });

    $.widget('mage.configure', {
        /**
         * Configuration phase. Prompt for hostname, database information, and options,
         * such as whether to enable SSL, referred to as secure options.
         */
        _create: function() {
            if (this.options.url) {
                this.element.on('click', $.proxy(function() {
                    location.href = this.options.url;
                }, this));
            } else {
                this.element.mage().validate();
                $(this.options.useSecureSelector).on('click', $.proxy(function(e) {
                    return e.target.checked ?
                        $(this.options.useSecureOptionsSelector).show() :
                        $(this.options.useSecureOptionsSelector).hide();
                }, this));
            }
        }
    });

    $.widget('mage.createAdmin', {
        /**
         * Create backend administrator login form validation. Enter user's name, email,
         * admin username, and password. Validate the form.
         */
        _create: function() {
            this.element.mage().validate({errorClass: 'mage-error', errorElement: 'div'});
        }
    });

    $.widget('mage.setLocale', {
        /**
         * Generate a new URL whenever a different locale is selected and refresh the
         * page to that new locale based URL.
         */
        _create: function() {
            this.element.on('change', $.proxy(function() {
                location.href = this.options.url + 'locale/' + this.element.val() +
                    '/?timezone=' + $('#timezone').val() + '&amp;currency=' + $('#currency').val();
            }, this));
        }
    });
})(jQuery);
