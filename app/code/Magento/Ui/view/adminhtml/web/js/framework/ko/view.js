define(['ko'], function(ko) {

    return {
        bind: function(el, model) {
            ko.applyBindings(model, el);
        }
    }
});