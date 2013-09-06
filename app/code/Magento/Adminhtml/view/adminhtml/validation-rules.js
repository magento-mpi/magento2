/**
 * Adminhtml client side validation rules
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
(function ($) {
    $.validator.addMethod('validate-rating', function() {
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
    }, 'Please select one of each ratings above.');
})(jQuery);
