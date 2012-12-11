/**
 * {license_notice}
 *
 * @category    validation
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true*/
(function($) {
    "use strict";

    $.validator.addMethod(
        "validate-one-checkbox-required-by-name",
        function(value, element, params) {
            var checkedCount = 0;
            if (element.type === 'checkbox') {
                $('[name="' + element.name + '"]').each(function() {
                    if ($(this).is(':checked')) {
                        checkedCount += 1;
                        return false;
                    }
                });
            }
            var container = '#' + params;
            if (checkedCount > 0) {
                $(container).removeClass('validation-failed');
                $(container).addClass('validation-passed');
                return true;
            } else {
                $(container).addClass('validation-failed');
                $(container).removeClass('validation-passed');
                return false;
            }
        },
        'Please select one of the options.'
    );

    $.widget("mage.validation", $.mage.validation, {
        /**
         * Check if form pass validation rules without submit
         * @return boolean
         */
        isValid: function() {
            return this.element.valid();
        },

        /**
         * Remove validation error messages
         */
        clearError: function() {
            if (arguments.length) {
                $.each(arguments, $.proxy(function(index, item) {
                    this.element.find(item).removeClass(this.options.errorClass)
                        .siblings(this.options.errorElement + '.' + this.options.errorClass).remove();
                }, this));
            } else {
                this.element.find(this.options.errorElement + '.' + this.options.errorClass).remove().end()
                    .find('.' + this.options.errorClass).removeClass(this.options.errorClass);
            }
        }
    });
})(jQuery);
