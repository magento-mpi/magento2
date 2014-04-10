/**
 * Bundle client side validation rules
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */
(function ($) {
    $.validator.addMethod('validate-greater-zero-based-on-option', function(v, el) {
        var optionType = $(el)
                .closest('.form-list')
                .prev('.fieldset-alt')
                .find('select.select-product-option-type'),
            optionTypeVal = optionType.val();
        v = Number(v) || 0;
        if (optionType && (optionTypeVal == 'checkbox' || optionTypeVal == 'multi') && v <= 0) {
            return false;
        }
        return true;
    }, 'Please enter a number greater 0 in this field.');
})(jQuery);
