/**
 * Rating validation rules
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    "jquery",
    "mage/validation"
], function($){

    $.validator.addMethod(
        'validate-rating',
        function () {
            var ratings = $('#detailed_rating').find('.field-rating'),
                noError = true;

            ratings.each(function (index, rating) {
                noError = noError && $(rating).find('input:checked').length > 0;
            });
            return noError;
        },
        'Please select one of each ratings above.');

});