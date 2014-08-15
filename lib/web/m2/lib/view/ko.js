define(['ko', 'm2/lib/bindings/date'], function (ko) {

  return {
    init: function (el, model) {
      ko.applyBindings(model, el);
    }
  }
});