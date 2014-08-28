define(['_'], function (_) {
  
  var RestClient = function (adapter) {
    this._adapter = adapter;
  };

  _.extend(RestClient.prototype, {
    read: function (params, id) {
      return this._adapter.read(params, id);
    },

    create: function (entry) {
      return this._adapter.create(entry);
    },

    remove: function (ids) {
      return this._adapter.remove(ids);
    }
  });

  return RestClient;
});