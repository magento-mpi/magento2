/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true browser:true */
jQuery(function ($) {
    'use strict';
    $.ajaxSetup({
        cache: false
    });

    var bootstrap = function() {

        /*
         * Show loader on ajax send
         */
        $('body').on('ajaxSend', function(e, jqxhr, settings) {
            if (settings && settings.showLoader) {
                wrapper.trigger('processStart');
            }
        });

        /*
         * Hide loader on ajax complete
         */
        $('body').on('ajaxComplete ajaxError', function(e, jqxhr, settings) {
            wrapper.trigger('processStop');
        });

        /**
         * Init all components defined via data-mage-init attribute
         * and subscribe init action to contentUpdated event
         */
        $.mage.init();
    };

    $(bootstrap);
});
