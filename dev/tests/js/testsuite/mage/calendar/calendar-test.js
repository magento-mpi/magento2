/**
 * {license_notice}
 *
 * @category    mage.calendar
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
CalendarTest = TestCase('CalendarTest');
CalendarTest.prototype.testCalendar = function () {
    /*:DOC +=
        <div>
            <input type="text" id="datepicker"/>
            <script type="text/javascript">
                //<![CDATA[
                $.mage.event.observe("mage.calendar.initialize", function (event, initData) {
                    var datepicker = {
                        inputSelector: "#datepicker",
                        locale: "",
                        options: {
                            buttonImage: "",
                            buttonText: "Select Date",
                            dateFormat: "mm-dd-yy",
                            yearRange: "2012:2015"
                        }
                    };
                    initData.datepicker.push(datepicker);
                });
                //]]>
            </script>
            <script type="text/javascript" src="/pub/lib/mage/calendar/calendar.js"></script>
        </div>
    */

    var datepicker = $.datepicker._getInst($('#datepicker')[0]);
    assertNotUndefined(datepicker);

    assertEquals("Select Date", datepicker.settings.buttonText);
    assertEquals("mm-dd-yy", datepicker.settings.dateFormat);
    assertEquals("button", datepicker.settings.showOn);
    assertEquals("2012:2015", datepicker.settings.yearRange);
};

