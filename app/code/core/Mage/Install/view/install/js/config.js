/**
 * {license_notice}
 *
 * @category    install locale
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true */
(function ($) {
    $.widget('mage.config', {
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
})(jQuery);