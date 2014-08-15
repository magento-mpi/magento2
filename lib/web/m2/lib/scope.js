define(function (EventBus, _, utils) {
  var
    EventBus = require('m2/lib/events'),
    _        = require('_'),
    utils    = require('m2/lib/utils'),
    ko       = require('ko');

  var Scope = function () {
    var mixin, i, field;

    if (this.mixins) {

      for (i = 0; i < this.mixins.length; i++) {
        mixin = this.mixins[i];

        for (field in mixin) {
          if (mixin.hasOwnProperty(field)) {
            if (field !== 'setUp') {
              this.constructor.prototype[field] = mixin[field];
            }
          }
        }

        mixin.setUp.call(this);
      }
    }

    this.initialize.apply(this, arguments);
  };

  Scope.extend = utils.extend;

  _.extend(Scope.prototype, {

    initialize: function () {},

    def: function (path, value) {
      utils.setValueByPathIn(this, path, ko.observable(value));

      return this;
    },

    defArray: function (path, arr) {
      utils.setValueByPathIn(this, path, ko.observableArray(arr));

      return this;
    }
    
  }, EventBus);

  return Scope;
});