/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true browser:true*/
define([
    "jquery",
    "jquery/ui",
    "jquery/template"
], function($){

    $.widget("mage.notification", {
        options: {
            templates: {
                global: '<div class="messages"><div class="message {{if error}}error{{/if}}"><div>${message}</div></div></div>'
            }
        },

        /**
         * Notification creation
         * @protected
         */
        _create: function() {
            $.each(this.options.templates, function(key, template) {
                $.template(key + 'Notification', template);
            });
            $(document).on('ajaxComplete ajaxError', $.proxy(this._add, this));
        },

        /**
         * Add new message
         * @protected
         * @param {Object} event object
         * @param {Object} jqXHR The jQuery XMLHttpRequest object returned by $.ajax()
         * @param {Object}
         */
        _add: function(event, jqXHR) {
            try {
                var response = $.parseJSON(jqXHR.responseText);
                if (response && response.error && response.html_message) {
                    this.element.find('[data-container-for=messages]').html(response.html_message);
                }
            } catch(e) {}
        }
    });

});
