define([
  'Magento_Ui/js/framework/provider/rest',
  'jquery',
  'storage',
  '_'
], function (RestProvider, $, lo, _) {

  var i = 0;
  function generateUniqueId() {
    return i++;
  }

  function resolve(def, data) {
    setTimeout(function () {
      def.resolve(data);
    }, _.random(500, 1000));
  }
  
  var RestLocalAdapter = function (resource) {
    this.resource = {
      name: resource,
      config: RestProvider.get(resource)
    };
  };

  _.extend(RestLocalAdapter.prototype, {
    read: function (id) {
      var
        deferred = $.Deferred(),
        collection,
        entry;

      if (id) {
        entry = _.findWhere(collection, { id: id });
        resolve(deferred, entry || {});
      } else {
        collection = lo.storage.get(this.resource.name);
        resolve(deferred, collection || []);
      }

      return deferred.promise();
    },

    create: function (data) {
      var deferred = $.Deferred();

      _.extend(data, { id: generateUniqueId() });
      lo.storage.push(this.resource.name, data);

      resolve(deferred, data);
      return deferred.promise();
    },

    update: function (id, data) {
      var
        resource = this.resource.name,
        deferred = $.Deferred(),
        collection = lo.storage.get(resource),
        entry = _.findWhere(collection, { id: id });

      _.extend(entry, data);
      lo.storage.set(resource, collection);
      resolve(deferred, entry);

      return deferred.promise();
    },

    'delete': function (ids) {
      var
        resource = this.resource.name,
        deferred = $.Deferred(),
        collection = lo.storage.get(resource),
        position, entry,
        deletedIds = [];

      ids.forEach(function (id) {
        entry = _.findWhere(collection, { id: id });
        position = _.indexOf(collection, entry);
        if (position >= 0) {
          collection.splice(position, 1);  
          deletedIds.push(id);
        }
      });

      lo.storage.set(resource, collection);
      resolve(deferred, deletedIds);

      return deferred.promise();
    }
  });

  return RestLocalAdapter;
});