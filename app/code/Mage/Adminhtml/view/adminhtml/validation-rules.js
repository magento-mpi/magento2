/**
 * Adminhtml client side validation rules
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
(function ($) {
    //validation for selected input type
    $.validator.addMethod('required-option-select', function(v, elm) {
        if (elm.getValue() == '') {
            return false;
        }
        return true;
    }, 'Select type of option');
    $.validator.addMethod('required-option-select-type-rows', function(v, elm) {
        var optionContainerElm = $(elm).closest('.form-list');
        var selectTypesFlag = false;
        selectTypeElements = optionContainerElm.find('.select-type-title');
        selectTypeElements.each(function(i, element){
            if ($(element).prop('id') && $(element).closest('tr').is(':visible')) {
                selectTypesFlag = true;
            }
        });
        elm.advaiceContainer = optionContainerElm.prop('id') + '_advice';
        return selectTypesFlag;
    }, 'Please add rows to option.');

    $.validator.addMethod('validate-rating', function(v) {
        var ratings = $('#detailed_rating').find('.field-rating');
        var inputs;
        var error = 1;

        ratings.each(function(i, rating) {
            if (i > 0) {
                inputs = $(rating).find('input');

                inputs.each(function(j, input) {
                    if ($(input).is(':checked')) {
                        error = 0;
                    }
                });

                if (error == 1) {
                    return false;
                }
            }
        });
        return !error;
    }, 'Please select one of each ratings above');
})(jQuery);