/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

require([
    "jquery",
    "Magento_Doc/js/m2",
    "mage/common",
    "mage/bootstrap"
],function(jQuery, M2){
    jQuery(function(){
       M2.init();
    });
    jQuery.migrateMute = true;
});