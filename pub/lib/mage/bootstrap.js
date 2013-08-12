/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Magento_Page
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
        $('body').on('ajaxSend processStart', function(e, jqxhr, settings) {
            if (settings && settings.showLoader || e.type === 'processStart') {
                $(e.target).mage('loader', {
                    icon: $('#loading_mask_loader img').attr('src'),
                    showOnInit: true
                });
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
