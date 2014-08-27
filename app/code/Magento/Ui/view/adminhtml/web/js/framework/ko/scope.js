define(function (require) {
  
  var
    Class    = require('Magento_Ui/js/framework/class'),
    EventBus = require('Magento_Ui/js/framework/events'),
    utils    = require('Magento_Ui/js/framework/utils'),
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