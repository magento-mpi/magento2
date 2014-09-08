define(['ko', 'jquery', 'mage/calendar'], function (ko, $) {
    var DEFAULT_DATE_FORMAT = 'mm/dd/yyyy';

    ko.bindingHandlers.datepicker = {
        init: function (el, valueAccessor) {
            var observable = valueAccessor();

            $(el).calendar({ dateFormat: DEFAULT_DATE_FORMAT });

            ko.utils.registerEventHandler(el, 'change', function (e) {
                var value = $(this).val();
                observable(value);
            });
        }
    }
});