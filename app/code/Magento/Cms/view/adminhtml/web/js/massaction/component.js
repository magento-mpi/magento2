define(function (require) {

  var
    MassAction  = require('Magento_Cms/js/massaction/entity'),
    View        = require('Magento_Ui/js/framework/ko/view'),
    Provider    = require('Magento_Ui/js/framework/provider/model');

  return function (el, config, initial) {
    
    var massAction = new MassAction(initial.massactions, initial.actions);
    
    View.bind(el, massAction);
  }
});