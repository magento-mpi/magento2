/**
 *
 *
 * @license     {}
 */

(function($) {
    $.widget("mage.validation", $.mage.validation, {
        options: {
            messagesId: 'messages',
            errorClass: 'validation-advice'
        },
        _create: function(){
            if(!this.options.frontendOnly && this.options.validationUrl) {
                this.options.submitHandler = $.proxy(this._ajaxValidate, this);
            }
            this._super('_create');
        },
        _ajaxValidate: function() {
            $.ajax({
                url: this.options.validationUrl,
                type: 'POST',
                data: this.element.serialize(),
                success: $.proxy(this._onSuccess, this),
                error: $.proxy(this._onError, this)
            });
        },
        /*
         * Process ajax success
         * @protected
         * @param {string} response test
         * @param {string} response status
         * @param {Object} The jQuery XMLHttpRequest object returned by $.ajax()
         */
        _onSuccess: function(responseText, status, jqXHR) {
            var response = $.parseJSON(responseText);
            if(response.attribute) {
                $('#' + response.attribute)
                    .addClass('validate-ajax-error')
                    .data('msg-validate-ajax-error', response.message);
                this.validate.element( "#" + response.attribute);
            }
            if (!response.error) {
                this.element[0].submit();
            }
        },
        /*
         * Process ajax error
         * @protected
         */
        _onError: function() {
            location.href = BASE_URL;
        }
    });
})(jQuery);
