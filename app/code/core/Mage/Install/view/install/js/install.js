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
    $.widget('mage.install', {
        _create: function() {
        },

        begin: function() {
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
        },

        config: function() {
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
        },

        createAdmin: function() {
            this.element.mage().validate({errorClass: 'mage-error', errorElement: 'div'});
        },

        changeUrl: function() {
            this.element.on('change', $.proxy(function() {
                location.href = this.options.url + 'locale/' + this.element.val() +
                    '/?timezone=' + $('#timezone').val() + '&amp;currency=' + $('#currency').val();
            }, this));
        }
    });
})(jQuery);
