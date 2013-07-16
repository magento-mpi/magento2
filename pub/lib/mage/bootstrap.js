/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Page
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

        /*
         * Show loader on ajax send
         */
        $('body').on('ajaxSend', function(e, jqxhr, settings) {
            if (settings && settings.showLoader) {
                // Check to make sure the loader is there on the page if not report it on the console.
                // NOTE that this check should be removed before going live. It is just an aid to help
                // in finding the uses of the loader that maybe broken.
                if (console && !$(e.target).parents('[data-role="loader"]').length) {
                    console.warn('Expected to start loader but did not find one in the dom');
                }
                if (settings.context) {
                    $(settings.context).trigger('processStart');
                }
                else {
                    $('body').trigger('processStart');
                }
            }
        });

        /*
         * Hide loader on ajax complete
         */
        $('body').on('ajaxComplete', function(e, jqxhr, settings) {
            if (settings && settings.showLoader) {
                if (settings.context) {
                    $(settings.context).trigger('processStop');
                }
                else {
                    $('body').trigger('processStop');
                }
            }
        });

        /**
         * Init all components defined via data-mage-init attribute
         * and subscribe init action to contentUpdated event
         */
        $.mage.init();
    };

    $(bootstrap);
});
