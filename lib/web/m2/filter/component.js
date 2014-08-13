define([
  'm2/controller',
  'm2/filter/model',
  'm2/lib/view/ng'
], function (controller, Filter, View) {
  return function (el, config, initial, scope) {

    var filter = new Filter(initial.query);
    var view = View.init(el, filter, 'cms.pages');

    controller.registerModel('simpleFilter', filter, 'cms.pages');
    controller.registerView(view, ['cms.pages']);

  };
});