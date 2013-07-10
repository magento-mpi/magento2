/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true browser:true */
jQuery(function ($,console) {
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
                $(e.target).trigger('processStart');
                // Check to make sure the loader is there on the page
                if (!$(e.target).element.find('[data-role="loader"]').length && console) {
                    console.warn('Expected to start loader but did not find one in the dom');
                }
            }
        });

        /*
         * Hide loader on ajax complete
         */
        $('body').on('ajaxComplete ajaxError', function(e, jqxhr, settings) {
            $(e.target).trigger('processStop');
        });

        /**
         * Init all components defined via data-mage-init attribute
         * and subscribe init action to contentUpdated event
         */
        $.mage.init();
    };

    $(bootstrap);
});
