define([
  'm2/controller',
  'm2/listing/model',
  'm2/lib/view/ng'
], function (controller, Listing, View) {
  return function (el, config, initial) {

    var listing = new Listing(initial);
    var view = View.init(el, listing, 'cms.pages');

    controller.registerModel('simpleListing', listing, 'cms.pages');
    controller.registerView(view, ['cms.pages']);

  };
});