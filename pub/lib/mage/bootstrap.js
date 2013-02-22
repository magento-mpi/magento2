/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true browser:true */
/*global FORM_KEY:true*/
jQuery(function ($) {
    'use strict';
    $.ajaxSetup({
        cache: false
    });

    var bootstrap = function() {
        /**
         * Init all components defined via data-mage-init attribute
         * and subscribe init action on contentUpdated event
         */
        $.mage.init();

    };

    $(bootstrap);
});
