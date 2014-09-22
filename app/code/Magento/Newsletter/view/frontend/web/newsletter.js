/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
define([
    "jquery",
    "jquery/ui",
    "mage/validation/validation"
], function($){

    $.widget('mage.newsletter', {
        options: {
            errorClass: 'mage-error'
        },
        _create: function() {
            $(this.options.formSelector)
                .validation({errorClass: this.options.errorClass});
            this.element.on('click', $.proxy(function(e) {
               if ($(e.target).val() === this.options.placeholder) {
                   $(e.target).val('');
               }
            }, this)).on('focusout', $.proxy(function(e) {
                setTimeout($.proxy(function() {
                    if ($.trim($(e.target).val()) === '') {
                        $(e.target).val(this.options.placeholder);
                    }
                }, this), 100);
            }, this));
        }
    });
});