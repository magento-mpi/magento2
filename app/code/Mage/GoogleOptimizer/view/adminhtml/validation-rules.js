/**
 * GoogleOptimizer client side validation rules
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleOptimizer
 * @copyright   {copyright}
 * @license     {license_link}
 */
(function ($) {
    $.validator.addMethod('validate-googleoptimizer', function(v,element) {
        var fieldEntry = false,
            self = false,
            validationResult = true,
            elements = $('.validate-googleoptimizer');

        if (elements.length) {
            elements.each(function() {
                var elm = $(this);
                if ((elm.val() != "none") && (elm.val() != null) && (elm.val().length != 0)) {
                    fieldEntry = true;
                    if (elm.prop('id') == element.id) {
                        self = true;
                    }
                } else {
                    validationResult = false;
                }
            });
        }
        if (fieldEntry && !validationResult && !self) {
            return false;
        }
        return true;
    }, 'This is a required field unless all the fields are empty.');
    $.validator.addMethod('validate-googleoptimizer-attributes', function(v,element) {
        return googleOptimizerCheckAttributesCount(element);
    }, 'Not more than 8 attributes allowed.');
})(jQuery);
