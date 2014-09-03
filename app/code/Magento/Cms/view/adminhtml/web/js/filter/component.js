define([
    './entity',
    'Magento_Ui/js/framework/provider/model'
], function(Filter, Provider) {

    return function(el, config, initial) {
        Provider.get('cms.pages.listing').done(function (listing) {
            var filter = new Filter(listing, config, initial);
            Provider.register('cms.pages.listing.filter', filter);
        });
    }
});