define([
  'Magento_Ui/js/framework/ko/scope',
  'Magento_Ui/js/framework/tools/fixtures'
], function (Scope, fixtures) {
  
  return Scope.extend({

    initialize: function (listing) {
      this.target = listing;
    },

    reloadListing: function () {
      this.target.reload();
    },

    empty: function () {
      fixtures.empty('cms.pages.listing');
    }
  });
});