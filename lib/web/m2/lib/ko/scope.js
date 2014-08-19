define(function (require) {
  
  var
    Class    = require('m2/lib/class'),
    EventBus = require('m2/lib/events'),
    utils    = require('m2/lib/utils'),
    ko       = require('ko');

  return Class.extend({

    mixins: [ EventBus ],

    def: function (path, value) {
      utils.setValueByPathIn(this, path, ko.observable(value));

      return this;
    },

    defArray: function (path, arr) {
      utils.setValueByPathIn(this, path, ko.observableArray(arr));

      return this;
    }
  });
});