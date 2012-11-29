/**
 * {license_notice}
 *
 * @category    mage date option validator
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function($) {
    $.widget('mage.dateOptionValidator', {
        _create: function() {
            var options = this.options;
            $.validator.addMethod(
                'validateDatetime' + options.dateOptionId,
                function() {
                    var dateTimeParts =$('.datetime-picker[id^="options_' + options.dateOptionId + '"]');
                    var hasWithValue = false, hasWithNoValue = false;
                    var pattern = /day_part$/i;
                    for (var i=0; i < dateTimeParts.length; i++) {
                        if (! pattern.test($(dateTimeParts[i]).attr('id'))) {
                            if ($(dateTimeParts[i]).val() === "") {
                                hasWithValue = true;
                            } else {
                                hasWithNoValue = true;
                            }
                        }
                    }
                    return hasWithValue ^ hasWithNoValue;
                },
                this.options.validationMessage
            );
            $.validator.setDefaults({ignore: ':hidden:not(input[name^="' + options.inputFieldNamePrefix + '"])'});
        }
    });
})(jQuery);
