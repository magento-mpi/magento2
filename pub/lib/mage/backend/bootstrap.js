/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true browser:true */
/*global FORM_KEY:true*/
jQuery(function ($) {
    $.ajaxSetup({
        /*
         * @type {string}
         */
        type: 'POST',

        /*
         * Ajax before send callback
         * @param {Object} The jQuery XMLHttpRequest object returned by $.ajax()
         * @param {Object}
         */
        beforeSend: function(jqXHR, settings) {
            if (!settings.url.match(new RegExp('[?&]isAjax=true',''))) {
                settings.url = settings.url.match(
                    new RegExp('\\?',"g")) ?
                    settings.url + '&isAjax=true' :
                    settings.url + '?isAjax=true';
            }
            if ($.type(settings.data) === "string" &&
                settings.data.indexOf('form_key=') === -1
            ) {
                settings.data += '&' + $.param({
                    form_key: FORM_KEY
                });
            } else {
                if (!settings.data) {
                    settings.data = {
                        form_key: FORM_KEY
                    };
                }
                if (!settings.data.form_key) {
                    settings.data.form_key = FORM_KEY;
                }
            }
        },

        /*
         * Ajax complete callback
         * @param {Object} The jQuery XMLHttpRequest object returned by $.ajax()
         * @param {string}
         */
        complete: function(jqXHR) {
            if (jqXHR.readyState === 4) {
                var jsonObject = jQuery.parseJSON(jqXHR.responseText);
                if (jsonObject.ajaxExpired && jsonObject.ajaxRedirect) {
                    window.location.replace(jsonObject.ajaxRedirect);
                }
            }
        }
    });

    var bootstrap = function() {
        /**
         * Init all components defined via data-mage-init attribute
         */
        $.mage.init();

        /*
         * Show loader on ajax send
         */
        $('body').on('ajaxSend', function(e) {
            $(e.target).mage('loader', {
                icon: $('#loading_mask_loader img').attr('src'),
                showOnInit: true
            });
        });

        /*
         * Initialization of notification widget
         */
         $('#messages').mage('notification');
    };

    $(bootstrap);
});
