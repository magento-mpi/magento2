define(['ko', 'moment', 'jquery'], function(ko, moment, $) {
    ko.bindingHandlers.date = {
        update: function(element, valueAccessor, allBindingsAccessor, viewModel) {
            var value = valueAccessor();
            var allBindings = allBindingsAccessor();
            var valueUnwrapped = ko.utils.unwrapObservable(value);

            // Date formats: http://momentjs.com/docs/#/displaying/format/
            var pattern = allBindings.format || 'MMM d, YYYY hh:mm A';

            var output = "-";
            if (valueUnwrapped !== null && valueUnwrapped !== undefined) {
                output = moment(valueUnwrapped).format(pattern);
            }

            if ($(element).is("input") === true) {
                $(element).val(output);
            } else {
                $(element).text(output);
            }
        }
    };
});