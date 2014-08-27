define(function (require) {

  var
    Fixtures  = require('Magento_Cms/js/fixtures/entity'),
    View      = require('Magento_Ui/js/framework/ko/view');

  return function (el, config, initial) {
    
    var fixtures = new Fixtures();
    
    View.bind(el, fixtures);
  }
});