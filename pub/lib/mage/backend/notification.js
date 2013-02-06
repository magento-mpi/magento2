/**
 * {license_notice}
 *
 * @category    mage
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true browser:true*/
(function($) {
    $.widget("mage.notification", {
        options: {
            appendMethod: 'append',
            appendSelector: '#messages',
            templates: {
                global: '<ul class="messages"><li class="{{if error}}error-msg{{/if}}"><ul>' +
                    '<li>{{html message}}</li></ul></li></ul>'
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
            this.element.on('ajaxComplete ajaxError', $.proxy(this._add, this));
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
                if (response && response.error) {
                    var appendElement = $(this.options.appendSelector).length ?
                        $(this.options.appendSelector) :
                        this.element;
                    appendElement[this.options.appendMethod]($.tmpl('globalNotification', response));
                }
            } catch(e) {}
        }
    });
})(jQuery);
