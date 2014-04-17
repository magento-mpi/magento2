/**
 * Rating validation rules
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */
(function ($) {
    $.validator.addMethod('validate-rating', function () {
        var ratings = $('#detailed_rating').find('.field-rating'),
            error = true;

        ratings.each(function (i, rating) {
            var inputs = $(rating).find('input');

            inputs.each(function (j, input) {
                if ($(input).is(':checked')) {
                    error = false;
                }
            });

            return error != true;
        });
        return !error;
    }, 'Please select one of each ratings above.');
})(jQuery);
