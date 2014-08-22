define([
    'Magento_Cms/js/massaction/entity',
    'm2/lib/ko/view',
    'm2/lib/provider/model'
], function (MassAction, View, Provider) {

  return function (el, config, initial) {
    
    var massAction = new MassAction(initial.massactions, initial.actions);
    
    View.bind(el, massAction);
  }
});