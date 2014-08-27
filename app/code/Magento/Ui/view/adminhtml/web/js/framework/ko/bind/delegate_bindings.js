define(['ko'], function (ko) {

  ko.bindingHandlers['delegateBindingsTo'] = {
    init: function (el, valueAccessor) {
      var viewmodel = valueAccessor();

      if (viewmodel) {
        console.log('viewmodel', viewmodel)
        ko.cleanNode(el);
        ko.applyBindingsToDescendants(viewmodel, el);
      }

      return { controlsDescendantBindings: true };
    }
  };

});