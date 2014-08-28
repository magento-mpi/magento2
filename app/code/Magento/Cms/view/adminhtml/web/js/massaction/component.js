define([
    './entity',
    'Magento_Ui/js/framework/ko/view',
    'Magento_Ui/js/framework/provider/model'
], function (MassAction, View, Provider) {

  return function (el, config, initial) {

    Provider.get('cms.pages.listing').done(function (listing) {
        var massAction = new MassAction(initial.massactions, initial.actions, listing);
        View.bind(el, massAction);
    });
  }
});