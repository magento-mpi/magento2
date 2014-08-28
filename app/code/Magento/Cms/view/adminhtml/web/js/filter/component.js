define([
    './entity',
    'Magento_Ui/js/framework/ko/view',
    'Magento_Ui/js/framework/provider/model'
], function(Filter, View, Provider) {

    return function(el, config, initial) {

        Provider.get('cms.pages.listing').done(function (listing) {
            var filter = new Filter(listing, config, initial);
            View.bind(el, filter);
        });
    }
});