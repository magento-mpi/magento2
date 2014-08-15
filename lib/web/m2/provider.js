define(['_', 'jquery', 'm2/lib/events'], function (_, $, EventBus) {
  var registry = {};

  return _.extend({}, EventBus, {
    register: function (name, model) {
      registry[name] = model;
      this.trigger(name + ':registered', model);
    },

    get: function (name) {
      var isReady = $.Deferred();

      if (registry[name]) {
        isReady.resolve(registry[name]);
      } else {
        this.on(name + ':registered', function (model) {
          isReady.resolve(model);
        });
      }

      return isReady.promise();
    }
  });
});