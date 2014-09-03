define([
    './entity',
    'Magento_Ui/js/framework/provider/model'
], function(Paging, Provider) {

    return function(el, config, initial) {
        Provider.get('cms.pages.listing').done(function(listing){
            var paging = new Paging(initial, config, listing);
            Provider.register('cms.pages.listing.pagination', paging);
        });
    }
});