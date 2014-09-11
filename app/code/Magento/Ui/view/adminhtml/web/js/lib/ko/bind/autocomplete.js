define([
    'ko',
    '_',
    'jquery',
    'jquery/autocomplete/jquery.autocomplete'
], function(ko, _, $, Autocomplete) {

    ko.bindingHandlers.autocomplete = {
        init: function(el, valueAccessor) {
            var options = extractOptionsFrom(valueAccessor);

            var storage = options.storage;
            if (ko.isObservable(storage)) {
                options.onSelect = function(newValue) {
                    storage(newValue);
                };
            }

            var autocomplete = new Autocomplete(el, options);

            var source = options.source;
            if (ko.isObservable(source)) {
                source.subscribe(function(newData) {
                    autocomplete.setOptions({
                        'lookup': newData
                    });
                    if (newData.length) {
                        $(el).focus();
                    }
                });
            }
        }
    }

    function extractOptionsFrom(accessor) {
        var options = {};
        var unwrapperAccessor = accessor();

        options.source = unwrapperAccessor.data;
        options.lookup = ko.utils.unwrapObservable(options.source);
        options.storage = unwrapperAccessor.writeTo;

        return options;
    }
});