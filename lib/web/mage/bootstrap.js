/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true browser:true */
define([
    "jquery",
    "m2/m2",
    "mage/mage"
], function($, M2){
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

        M2.init();
    };

    $(bootstrap);

});
