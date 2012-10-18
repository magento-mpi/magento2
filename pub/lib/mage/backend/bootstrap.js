/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
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
        beforeSend: function(jqXHR, settings){
            if (!settings.url.match(new RegExp('[?&]isAjax=true',''))) {
                settings.url = settings.url.match(
                    new RegExp('\\?',"g")) ?
                    settings.url + '&isAjax=true' :
                    settings.url + '?isAjax=true';
            }
            if ($.type(settings.data) === "string"
                && settings.data.indexOf('form_key=') == -1
                ) {
                settings.data += '&' + $.param({
                    form_key: FORM_KEY
                });
            } else {
                if (!settings.data) {
                    this.options.data = {
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
        complete: function(jqXHR, status){
            if (jqXHR.readyState == 4) {
                try {
                    var jsonObject = jQuery.parseJSON(jqXHR.responseText);
                    if (jsonObject.ajaxExpired && jsonObject.ajaxRedirect) {
                        window.location.replace(jsonObject.ajaxRedirect);
                        throw new SessionError('session expired');
                    }
                } catch (e) {
                    if (e instanceof SessionError) {
                        return;
                    }
                }
            }
        }
    });

    var bootstrap = function(){
        $('*[data-widget-button]').button();

        $('body').on('ajaxSend', function(e){
            $(e.target).loader().loader('show');
        });

        if ($('#messages').length) {
            $('#messages').notification();
        }
    };

    $(document).ready(function(){
        bootstrap();
    });
});
