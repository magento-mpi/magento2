define([
    './entity',
    'Magento_Ui/js/framework/provider/model'
], function (Fixtures, Provider) {

    return function (el, config, initial) {
      Provider.get('cms.pages.listing').done(function (listing) {
          var fixtures = new Fixtures(listing);
          Provider.register('cms.pages.listing.fixtures', fixtures);
      });
    }
});