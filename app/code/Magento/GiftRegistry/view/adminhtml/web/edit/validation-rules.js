/**
 * GiftRegistry client side validation rules
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*global parts:true*/
(function ($) {
    $.validator.addMethod('attribute-code', function(v, element){
        var resultFlag = true,
            select = $($('#' + $(element).prop('id').sub('_code','_type')));
        $.each(select.find('option'), function(i, option) {
            parts = $(option).val().split(':');
            if (parts[1] !== undefined && parts[1] == v) {
                resultFlag = false;
            }
        });
        return resultFlag;
    }, 'Please use a different input type for this code.');

    $.validator.addMethod('required-option-select-rows', function(v, elm) {
        var optionContainerElm = $(elm).closest('fieldset');
        return !!$(optionContainerElm).find('tr:not(.no-display) .select-option-code').length;
    }, 'Please add rows to option.');
})(jQuery);
