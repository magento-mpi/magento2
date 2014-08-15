define(function (require) {

  var
    MassAction  = require('Magento_Cms/js/massaction/entity'),
    View        = require('m2/lib/view/ko'),
    Provider    = require('m2/provider');

  return function (el, config, initial) {
    
    var massAction = new MassAction(initial.massactions, initial.actions);
    
    View.init(el, massAction);
  }
});