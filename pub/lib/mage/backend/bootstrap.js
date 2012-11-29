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
    $.extend(true, $, {
        mage: {
            isJSON : function(json){
                json = json.replace(/\\["\\\/bfnrtu]/g, '@');
                json = json.replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']');
                json = json.replace(/(?:^|:|,)(?:\s*\[)+/g, '');
                return (/^[\],:{}\s]*$/.test(json))
            }
        }
    });

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
                if($.mage.isJSON(jqXHR.responseText)) {
                    var jsonObject = jQuery.parseJSON(jqXHR.responseText);
                    if (jsonObject.ajaxExpired && jsonObject.ajaxRedirect) {
                        window.location.replace(jsonObject.ajaxRedirect);
                    }
                }
            }
        }
    });

    var bootstrap = function() {
        /*
         * Initialization of button widgets
         */
        $('*[data-widget-button]').button();

        /*
         * Show loader on ajax send
         */
        $('body').on('ajaxSend processStart', function(e) {
            $(e.target).loader({
                icon: $('#loading_mask_loader img').attr('src')
            }).loader('show');
        });

        /*
         * Initialization of notification widget
         */
        if ($('#messages').length) {
            $('#messages').notification();
        }
    };

    $(document).ready(bootstrap);
});
