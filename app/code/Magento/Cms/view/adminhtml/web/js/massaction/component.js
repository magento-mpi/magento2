define([
    'Magento_Cms/js/massaction/entity',
    'Magento_Ui/js/framework/ko/view',
    'Magento_Ui/js/framework/provider/model'
], function (MassAction, View, Provider) {

  return function (el, config, initial) {
    
    var massAction = new MassAction(initial.massactions, initial.actions);
    
    View.bind(el, massAction);
  }
});