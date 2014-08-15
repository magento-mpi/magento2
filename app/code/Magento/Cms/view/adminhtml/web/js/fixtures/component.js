define(function (require) {

  var
    Fixtures  = require('Magento_Cms/js/fixtures/entity'),
    View      = require('m2/lib/view/ko');

  return function (el, config, initial) {
    
    var fixtures = new Fixtures();
    
    View.init(el, fixtures);
  }
});