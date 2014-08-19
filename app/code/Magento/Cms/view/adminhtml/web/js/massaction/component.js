define(function (require) {

  var
    MassAction  = require('Magento_Cms/js/massaction/entity'),
    View        = require('m2/lib/ko/view'),
    Provider    = require('m2/lib/provider/model');

  return function (el, config, initial) {
    
    var massAction = new MassAction(initial.massactions, initial.actions);
    
    View.bind(el, massAction);
  }
});