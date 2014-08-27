define(['_'], function (_) {
  var resources = {};

  return {
    get: function (name) {
      resources[name];
    },

    add: function (name, params) {
      resources[name] = params;
    }
  }
});