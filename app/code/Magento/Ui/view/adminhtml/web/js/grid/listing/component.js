define([
    './listing',
    'Magento_Ui/js/framework/ko/view',
    'Magento_Ui/js/registry/registry'
], function(Listing, View, registry) {
    'use strict';

    return function(el, config, initial) {
        var listing = new Listing(config, initial);

        registry.set(
            config.namespace + ':storage',
            listing.storage
        );

        View.bind(el, listing);
    }
});