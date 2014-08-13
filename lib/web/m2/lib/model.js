define(['m2/lib/events', '_', 'm2/lib/utils'], function (EventBus, _, utils) {
  var Model = function () {
    this.props = {};
    this.$siblings = {};
    this.initialize.apply(this, arguments);
  };

  Model.extend = extend;

  _.extend(Model.prototype, {

    get: function (path) {
      return utils.getValueByPathIn(this.props, path);
    },

    set: function (path, value) {
      utils.setValueByPathIn(this.props, path, value, true);
      this.trigger('change');
    }
  }, new EventBus);

  return Model;

  function extend(protoProps, staticProps) {
    var parent = this;
    var child;

    if (protoProps && _.has(protoProps, 'constructor')) {
      child = protoProps.constructor;
    } else {
      child = function(){ return parent.apply(this, arguments); };
    }

    _.extend(child, parent, staticProps);

    var Surrogate = function(){ this.constructor = child; };
    Surrogate.prototype = parent.prototype;
    child.prototype = new Surrogate;

    if (protoProps) _.extend(child.prototype, protoProps);

    child.__super__ = parent.prototype;

    return child;
  }
});