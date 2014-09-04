define(['ko', 'Magento_Ui/js/lib/registry/registry'], function(ko, registry) {

    ko.bindingHandlers.scope = {
        init: function(el, valueAccessor, allBindings, viewModel, bindingContext) {
            var component = valueAccessor();
            var childBindingContext;

            registry.get(component, function(component) {
                childBindingContext = bindingContext.createChildContext(component);
                ko.applyBindingsToDescendants(childBindingContext, el);
            });

            return {
                controlsDescendantBindings: true
            };
        }
    };
});