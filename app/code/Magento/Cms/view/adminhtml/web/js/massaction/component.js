define([
    './entity',
    'Magento_Ui/js/framework/provider/model'
], function (MassAction, Provider) {

  return function (el, config, initial) {

    Provider.get('cms.pages.listing').done(function (listing) {
        var massAction = new MassAction(initial.actions, listing);
        Provider.register('cms.pages.listing.massaction', massAction);
    });
  }
});