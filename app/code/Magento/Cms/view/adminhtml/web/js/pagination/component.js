define([
    './entity',
    'Magento_Ui/js/framework/ko/view',
    'Magento_Ui/js/framework/provider/model'
], function(Paging, View, Provider) {

    return function(el, config, initial) {
        Provider.get('cms.pages.listing').done(function(listing){
            var paging = new Paging(initial, config, listing);

            View.bind(el, paging);   
        });
    }
});