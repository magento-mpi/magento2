/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Magento_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true browser:true */
jQuery(function ($, console) {
    'use strict';
    $.ajaxSetup({
        cache: false
    });

    var bootstrap = function() {
        /**
         * Init all components defined via data-mage-init attribute
         * and subscribe init action to contentUpdated event
         */
        $.mage.init();
    };

    $(bootstrap);
});
