/**
 * {license_notice}
 *
 * @category    frontend calendar
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*global document:true mage:true jQuery:true*/
(function ($) {
    /*
     * Full documentation for the datepicker, including all options, APIs, and events can be found
     * here: http://docs.jquery.com/UI/Datepicker. Note that all of the required options must be set by
     * the initialization event.
     */
    var datepickerOptions = {
        buttonImage: null, /* The URL for the calendar icon. Reguired. */
        buttonImageOnly: true, /* The buttonImage is the trigger. Displays image, but not button. */
        buttonText: null, /* Text displayed when hovering over buttonImage. Required.*/
        changeMonth: true, /* Dropdown selectable month. */
        changeYear: true, /* Dropdown selectable year, if yearRange includes multiple years. */
        showButtonPanel: true, /* Show the Today and Done buttons. */
        showOn: 'button', /* The datepicker only appears when the buttonImage is clicked. */
        showWeek: true, /* Show the week of the year column. */
        yearRange: null /* The year range. Defaults to current year + or - 10 years. Required. */
        /* The required format for the yearRange option is ####:#### (e.g. 2012:2015). */
    };

    $(document).ready(function () {
        var calendarInit = {
            datepicker: [] /* Array of datepickers. Possibly more than one on any given page. */
        };
        mage.event.trigger("mage.calendar.initialize", calendarInit);
        $.each(calendarInit.datepicker, function (index, value) {
            $(value.inputSelector).datepicker(
                /* Merge datepicker options. Include localized settings which may default to English. */
                $.extend(datepickerOptions, $.datepicker.regional[value.locale], value.options)
            );
        });
    });

}(jQuery));
