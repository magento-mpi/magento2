define(['_'], function (_) {
  
  var RestClient = function (adapter) {
    this._adapter = adapter;
  };

  _.extend(RestClient.prototype, {
    read: function (id) {
      return this._adapter.read(id);
    },

    create: function (data) {
      return this._adapter.create(data);
    },

    update: function (id, data) {
      return this._adapter.update(id, data);
    },

    remove: function (ids) {
      return this._adapter['delete'].call(this._adapter, ids);
    }
  });

  return RestClient;
});