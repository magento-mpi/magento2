/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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