/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true browser:true */
define([
    "jquery",
    "mage/apply/main"
], function($, mage){
    'use strict';
    
    $.ajaxSetup({
        cache: false
    });

    var bootstrap = function() {
        /**
         * Init all components defined via data-mage-init attribute
         * and subscribe init action to contentUpdated event
         */
        mage.apply();
    };

    $(bootstrap);
});
