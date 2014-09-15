/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

test('DatepickerBinding', function () {
    expect(1);

    var element    = $('#datepicker'),
        observable = ko.observable(),
        openBtn,
        todayBtn,
        todayDate,
        dateFormat;

    ko.applyBindingsToNode(element, {
        datepicker: observable
    });

    dateFormat = $(element).datepicker('option', 'dateFormat');
    todayDate = moment().format(dateFormat);

    btn      = $('img.ui-datepicker-trigger');
    todayBtn = $('[data-handler="today"]');

    btn.click();
    todayBtn.click();

    equal(todayDate, moment(observable()).format(dateFormat));
});