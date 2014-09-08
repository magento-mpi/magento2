define(['ko', 'jquery', 'mage/calendar'], function (ko, $) {
    ko.bindingHandlers.datepicker = {
        init: function (el, valueAccessor) {
            var observable = valueAccessor();

            $(el).calendar({ dateFormat: 'mm/dd/yyyy' });

            ko.utils.registerEventHandler(el, 'change', function (e) {
                var value = $(this).val();
                observable(value);
            });
        }
    }
});