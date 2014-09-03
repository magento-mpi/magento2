define([
    './listing',
    'Magento_Ui/js/registry/registry'
], function(Listing, registry) {
    'use strict';

    return function(el, config, initial) {
        var listing = new Listing(config, initial),
            namespace = config.namespace;


        registry
        .set(
            namespace + ':storage',
            listing.storage
        )
        .set(
            namespace + ':listing',
            listing
        );
    }
});