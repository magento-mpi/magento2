define(function (require) {

  var
    Listing      = require('./entity'),
    DataProvider = require('Magento_Ui/js/framework/provider/model'),
    RestProvider = require('Magento_Ui/js/framework/provider/rest');

  return function (el, config, initial) {

    RestProvider.add('cms.pages', { url: 'cms/pages' });
    
    var listing = new Listing(initial, config);
    DataProvider.register('cms.pages.listing', listing);
  }
});