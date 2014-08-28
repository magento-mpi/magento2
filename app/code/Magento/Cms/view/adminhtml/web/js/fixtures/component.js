define([
    './entity',
    'Magento_Ui/js/framework/ko/view',
    'Magento_Ui/js/framework/provider/model'
], function (Fixtures, View, Provider) {

  return function (el, config, initial) {

    Provider.get('cms.pages.listing').done(function (listing) {
        var fixtures = new Fixtures(listing);
        
        View.bind(el, fixtures);
    });
  }
});