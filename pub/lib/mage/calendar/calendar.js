/**
 * {license_notice}
 *
 * @category    frontend calendar
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function ($) {

    var datepickerOptions = {
        buttonImage: null, /* The calendar icon. Trigger sets this. */
        buttonImageOnly: true, /* The buttonImage is the trigger. Displays image, but not button. */
        buttonText: null, /* Text displayed when hovering over buttonImage. Trigger sets this. */
        changeMonth: true, /* Dropdown selectable month. */
        changeYear: true, /* Dropdown selectable year, if yearRange includes multiple years. */
        showButtonPanel: true, /* Show the Today and Done buttons. */
        showOn: 'button', /* The datepicker only appears when the buttonImage is clicked. */
        showWeek: true, /* Show the week of the year column. */
        yearRange: null /* The year range. Defaults to current year + or - 10 years. Trigger sets this. */
    };

    var calendarInit = {
        datepicker: [] /* Array of datepickers. Possibly more than one on any given page. */
    };

    $(document).ready(function () {
        mage.event.trigger("mage.calendar.initialize", calendarInit);
        $.each(calendarInit.datepicker, function (index, value) {
            $(value.inputSelector).datepicker(
                $.extend(datepickerOptions,
                    /* Merge datepicker options. Include localized settings or default to English. */
                    $.datepicker.regional[mage.language.code] || $.datepicker.regional[''], value.options)
            );
        });
        $('.ui-datepicker-trigger').each(function () {
            /* Vertically center the buttonImage in the middle of the input field. */
            $(this).addClass('v-middle');
        });
    });

}(jQuery));