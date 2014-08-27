define(function (require) {

  var
    Listing      = require('Magento_Cms/js/listing/entity'),
    View         = require('Magento_Ui/js/framework/ko/view'),
    DataProvider = require('Magento_Ui/js/framework/provider/model'),
    RestProvider = require('Magento_Ui/js/framework/provider/rest');

  return function (el, config, initial) {
    
    var listing = new Listing(initial, config);
    
    View.bind(el, listing);

    DataProvider.register('cms.pages.listing', listing);
    RestProvider.add('cms.pages', { url: 'cms/pages' });
  }
});