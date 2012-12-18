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
        "validate-grouped-qty",
        function(value, element, params) {
            var result = false;
            var total = 0;
            $(params).find('input:text').each(function(i, e) {
                var _e = $(e);
                if (_e.data('validate').substring('validate-grouped-qty')) {
                    var val = _e.val();
                    if (val && val.length > 0) {
                        result = true;
                        var valInt = parseInt(val, 10) || 0;
                        if (valInt >= 0) {
                            total += valInt;
                        } else {
                            result = false;
                            return result;
                        }
                    }
                }
            });
            if (result && total > 0) {
                return true;
            }
            return false;
        },
        'Please specify the quantity of product(s).'
    );

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
