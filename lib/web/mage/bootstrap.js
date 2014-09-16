/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true browser:true */
define([
    "jquery",
    "mage/apply/main",
    "mage/mage"
], function($, Mage){
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

        Mage.apply();
    };

    $(bootstrap);

});
