define(function (require) {

  var
    Listing      = require('Magento_Cms/js/listing/entity'),
    View         = require('m2/lib/ko/view'),
    DataProvider = require('m2/lib/provider/model'),
    RestProvider = require('m2/lib/provider/rest');

  return function (el, config, initial) {
    
    var listing = new Listing(initial, config);
    
    View.bind(el, listing);

    DataProvider.register('cms.pages.listing', listing);
    RestProvider.add('cms.pages', { url: 'cms/pages' });
  }
});