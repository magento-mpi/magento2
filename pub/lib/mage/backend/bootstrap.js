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
    'use strict';
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
            var form_key = typeof FORM_KEY !== 'undefined' ? FORM_KEY : null;
            if (!settings.url.match(new RegExp('[?&]isAjax=true',''))) {
                settings.url = settings.url.match(
                    new RegExp('\\?',"g")) ?
                    settings.url + '&isAjax=true' :
                    settings.url + '?isAjax=true';
            }
            if (!settings.data) {
                settings.data = {
                    form_key: form_key
                };
            } else if ($.type(settings.data) === "string"
                && settings.data.indexOf('form_key=') === -1) {
                settings.data += '&' + $.param({
                    form_key: form_key
                });
            } else if($.isPlainObject(settings.data) && !settings.data.form_key) {
                settings.data.form_key = form_key;
            }
        },

        /*
         * Ajax complete callback
         * @param {Object} The jQuery XMLHttpRequest object returned by $.ajax()
         * @param {string}
         */
        complete: function(jqXHR) {
            if (jqXHR.readyState === 4) {
                try {
                    var jsonObject = jQuery.parseJSON(jqXHR.responseText);
                    if (jsonObject.ajaxExpired && jsonObject.ajaxRedirect) {
                        window.location.replace(jsonObject.ajaxRedirect);
                    }
                } catch(e) {}
            }
        }
    });

    var mageInit = function(context) {
            // Temporary solution, will be replaced when plug-in "mage" will be merged to master
            var collection = context ?
                context.add(context.find('[data-mage-init]')) :
                $('[data-mage-init]');


            collection.each(function(){
                var inits = $(this).data('mage-init') || {};
                $.each(inits, $.proxy(function(key, args){
                    $(this)[key].apply($(this), $.makeArray(args));
                }, this));
            });
        },
        bootstrap = function() {
            mageInit();

            /*
             * Initialization of button widgets
             */
            $('*[data-widget-button]').button();

            /*
             * Show loader on ajax send
             */
            $('body').on('ajaxSend processStart', function(e, jqxhr, settings) {
                if (settings && settings.showLoader || e.type === 'processStart') {
                    $(e.target).loader({
                        icon: $('#loading_mask_loader img').attr('src')
                    }).loader('show');
                }
            });

            /*
             * Initialization of notification widget
             */
            if ($('#messages').length) {
                $('#messages').notification();
            }
        };

    $(document).ready(function() {bootstrap();});
    $(document).on('contentUpdated', function(e) {
        mageInit($(e.target));
    });
});
