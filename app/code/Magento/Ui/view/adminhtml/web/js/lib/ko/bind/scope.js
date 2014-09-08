define([
    'ko',
    'Magento_Ui/js/lib/registry/registry'
], function(ko, registry) {

    function getMultiple(bindings, viewModel, callback) {
        var key,
            components,
            ctx;

        components = [];

        ctx = {
            parent: viewModel
        };

        for (key in bindings) {
            components.push(bindings[key]);
        }

        registry.get(components, function() {

            for (key in bindings) {
                ctx[key] = registry.get(bindings[key]);
            }

            callback(ctx);
        });
    }

    function applyComponents(el, bindingContext, component) {
        component = bindingContext.createChildContext(component);
        ko.applyBindingsToDescendants(component, el);
    }

    ko.bindingHandlers.scope = {
        init: function(el, valueAccessor, allBindings, viewModel, bindingContext) {
            var component = valueAccessor(),
                apply = applyComponents.bind(this, el, bindingContext);

            typeof component === 'object' ?
                getMultiple(component, viewModel, apply) :
                registry.get(component, apply);

            return {
                controlsDescendantBindings: true
            };
        }
    };
});