define([
    './listing',
    'Magento_Ui/js/lib/registry/registry'
], function(Listing, registry) {
    'use strict';

    return function(el, data) {
        var listing = new Listing( data ),
            namespace = data.config.namespace;

        registry
            .set(namespace + ':storage', listing.storage)
            .set(namespace + ':listing', listing);
    }
});