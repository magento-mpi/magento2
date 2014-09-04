define([
    './paging',
    'Magento_Ui/js/lib/registry/registry'
], function(Paging, registry) {
    'use strict';

    return function(el, data) {
        var config = data.config,
            namespace = config.namespace;

        registry.get(namespace + ':storage', function(storage) {
            var paging;

            config.storage = storage;

            paging = new Paging(config);

            registry.set(namespace + ':paging', paging);
        });
    }
});