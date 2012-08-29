/**
 * {license_notice}
 *
 * @category    mage.design_editor
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
DaterangeTest = TestCase('DaterangeTest');
DaterangeTest.prototype.testInit = function() {
    /*:DOC +=
     <div id="date-range" />
     */
    var dateRange = jQuery('#date-range').date_range();
    assertEquals(true, dateRange.is(':mage-date_range'));
    dateRange.calendar('destroy');
};
DaterangeTest.prototype.testInitDateRangeDatepickers = function() {
    /*:DOC +=
     <div id="date-range">
         <input type="text" id="from" />
         <input type="text" id="to" />
     </div>
     */
    var options = {
            from: {
                id: "from"
            },
            to: {
                id: "to"
            }
        },
        dateRange = $('#date-range').date_range(options),
        from = $('#'+options.from.id),
        to = $('#'+options.to.id);

    assertEquals(true, from.hasClass('hasDatepicker'));
    assertEquals(true, to.hasClass('hasDatepicker'));
    dateRange.date_range('destroy');
};
DaterangeTest.prototype.testDestroy = function() {
    /*:DOC +=
     <div id="date-range">
     <input type="text" id="from" />
     <input type="text" id="to" />
     </div>
     */
    var options = {
        from: {
            id: "from"
        },
        to: {
            id: "to"
        }
    },
        dateRange = $('#date-range').date_range(options),
        from = $('#'+options.from.id),
        to = $('#'+options.to.id),
        dateRangeExist = dateRange.is(':mage-date_range'),
        fromExist = from.hasClass('hasDatepicker'),
        toExist = to.hasClass('hasDatepicker');

    dateRange.date_range('destroy');
    assertEquals(true, dateRangeExist != dateRange.is(':mage-date_range'));
    assertEquals(true, fromExist != from.hasClass('hasDatepicker'));
    assertEquals(true, toExist != to.hasClass('hasDatepicker'));
};