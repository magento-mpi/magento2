/**
 *
 * @license     {}
 */
(function($) {
    $.widget("mage.notification", {
        options: {
            templates: {
                global: '<ul class="messages"><li class="{{if error}}error-msg{{/if}}"><ul><li>${message}</li></ul></li></ul>',
            }
        },
        _create: function(){
            $.each(this.options.templates, function(key, template) {
                $.template(key + 'Notification', template);
            })
            $(document).on('ajaxComplete ajaxError', $.proxy(this._add, this));
        },
        _add: function(e, jqXHR, options) {
            var response = $.parseJSON(jqXHR.responseText);
            if(response.error) {
                this.element.append($.tmpl('globalNotification', response));
            }
        }
    })
})(jQuery);