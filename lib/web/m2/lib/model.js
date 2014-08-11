define(['m2/lib/events', '_', 'm2/lib/utils'], function (EventBus, _, utils) {
  var Model = function (data) {
    this.attrs = data;
  };

  _.extend(Model.prototype, {

    get: function (path) {
      return utils.getValueByPathIn(this.attrs, path);
    },

    set: function (path, value) {
      utils.setValueByPathIn(this.attrs, path, value, true);
    }

  }, new EventBus);

  return Model;
});