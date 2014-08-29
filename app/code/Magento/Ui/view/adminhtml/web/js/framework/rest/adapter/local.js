define([
    '_',
    'Magento_Ui/js/framework/tools/local_backend',
    'jquery'
], function (_, LocalBackend, $) {

    var RestLocalAdapter = function (resource) {
        this.backend = new LocalBackend(resource);
    };

    _.extend(RestLocalAdapter.prototype, {
        constructor: RestLocalAdapter,

        remove: function (idOrIds) {
            var isReady = $.Deferred();

            var result = idOrIds === 'array' ? removeCollection(idOrIds) : this.backend.removeOne(idOrIds);

            resolve(isReady, result);
            return isReady.promise();
        },

        read: function (params, id) {
            var isReady = $.Deferred();
            var result;

            if (id) {
                result = this.backend.readOne(id);
            } else {
                result = this.backend.readCollection(params);
            }

            resolve(isReady, result);
            return isReady.promise();
        },

        create: function (entry) {
            var isReady = $.Deferred();

            var result = this.backend.create(entry);

            resolve(isReady, result);
            return isReady.promise();
        }
    });

    return RestLocalAdapter;

    function resolve(deferred, data) {
        setTimeout(function () {
            deferred.resolve(data);
        }, _.random(400, 700));
    }
});