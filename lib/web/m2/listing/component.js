define(['m2/controller', 'm2/lib/model'], function (controller, Model) {
  return function (el, config, initial) {

    var listing = new Model({ items: initial.items });

    controller.registerModel(listing, 'cms.pages');

  };
});