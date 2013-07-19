/**
 * {license_notice}
 *
 * @category    validation - dob rule
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true*/
(function($) {
    "use strict";
    $.validator.addMethod(
        'validate-dob',
        function (val, element, params) {
            $('.customer-dob').find('.' + this.settings.errorClass).removeClass(this.settings.errorClass);
            var dayVal = $(params[0]).find('input:text').val(),
                monthVal = $(params[1]).find('input:text').val(),
                yearVal = $(params[2]).find('input:text').val(),
                dobLength = dayVal.length + monthVal.length + yearVal.length;
            if (params[3] && dobLength === 0) {
                this.dobErrorMessage = 'This is a required field.';
                return false;
            }
            if (!params[3] && dobLength === 0) {
                return true;
            }
            var day = parseInt(dayVal, 10) || 0,
                month = parseInt(monthVal, 10) || 0,
                year = parseInt(yearVal, 10) || 0,
                curYear = (new Date()).getFullYear();
            if (!day || !month || !year) {
                this.dobErrorMessage = 'Please enter a valid full date.';
                return false;
            }
            if (month < 1 || month > 12) {
                this.dobErrorMessage = 'Please enter a valid month (1-12).';
                return false;
            }
            if (year < 1900 || year > curYear) {
                this.dobErrorMessage = $.mage.__('Please enter a valid year (1900-%d).').replace('%d', curYear);
                return false;
            }
            var validateDayInMonth = new Date(year, month, 0).getDate();
            if (day < 1 || day > validateDayInMonth) {
                this.dobErrorMessage = $.mage.__('Please enter a valid day (1-%d).').replace('%d', validateDayInMonth);
                return false;
            }
            var today = new Date(),
                dateEntered = new Date();
            dateEntered.setFullYear(year, month - 1, day);
            if (dateEntered > today) {
                this.dobErrorMessage = $.mage.__('Please enter a date in the past.');
                return false;
            }

            day = day % 10 === day ? '0' + day : day;
            month = month % 10 === month ? '0' + month : month;
            $(element).val(month + '/' + day + '/' + year);
            return true;
        },
        function(){
            return this.dobErrorMessage;
        }
    );
})(jQuery);
