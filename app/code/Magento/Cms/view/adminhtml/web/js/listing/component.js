define(function (require) {

  var
    Listing  = require('Magento_Cms/js/listing/entity'),
    View     = require('m2/lib/view/ko'),
    Provider = require('m2/provider');

  return function (el, config, initial) {
    
    var listing = new Listing(initial, config);
    
    View.init(el, listing);

    Provider.register('cms.pages.listing', listing);
  }
});