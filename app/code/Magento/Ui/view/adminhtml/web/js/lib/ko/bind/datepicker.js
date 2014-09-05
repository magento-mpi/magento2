define(['ko', 'jquery', 'mage/calendar'], function (ko, $) {
    ko.bindingHandlers.datepicker = {
        init: function (el, valueAccessor) {
            var observable = valueAccessor();

            $(el).calendar();
        }
    }
});