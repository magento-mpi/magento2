define(['ko', 'Magento_Ui/js/framework/provider/model'], function (ko, Provider) {

    ko.bindingHandlers.scope = {
        init: function (el, valueAccessor, allBindings, viewModel, bindingContext) {
            var component = valueAccessor();
            var childBindingContext;

            Provider.get(component).done(function (component) {
                childBindingContext = bindingContext.createChildContext(component);
                console.log('binding context', childBindingContext, el)
                ko.applyBindingsToDescendants(childBindingContext, el);
            });

            return { controlsDescendantBindings: true };
        }
    };
});