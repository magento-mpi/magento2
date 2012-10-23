/**
 *
 *
 * @license     {}
 */

(function($) {
    var init = $.validator.prototype.init;
    $.extend(true, $.validator.prototype, {
        /**
         * validator initialization
         */
        init: function() {
            init.apply(this, arguments);
            var highlight = this.settings.highlight;
            this.settings.highlight = function (element, errorClass, validClass) {
                highlight.apply(this, arguments);
                $(element).trigger('highlight.validate');
            }
        },

        /**
         * Focus invalid fields
         */
        focusInvalid: function() {
            if (this.settings.focusInvalid) {
                try {
                    $(this.errorList.length && this.errorList[0].element || [])
                        .focus()
                        .trigger("focusin");
                } catch (e) {
                    // ignore IE throwing errors when focusing hidden elements
                }
            }
        }
    });

    $.widget("mage.validation", $.mage.validation, {
        options: {
            messagesId: 'messages',
            ignore: "",
            errorElement: 'label',
            errorUrl: BASE_URL
        },

        /**
         * Validation creation
         * @protected
         */
        _create: function() {
            if (!this.options.frontendOnly && this.options.validationUrl) {
                this.options.submitHandler = $.proxy(this._ajaxValidate, this);
            } else {
                this.options.submitHandler = $.proxy(function(){this.element[0].submit();}, this);
            }
            this.element.on('resetElement', function(e, data) {$(e.target).rules('remove');});
            this._super('_create');
        },

        /**
         * ajax validation
         * @protected
         */
        _ajaxValidate: function() {
            $.ajax({
                url: this.options.validationUrl,
                type: 'POST',
                dataType: 'json',
                data: this.element.serialize(),
                context: this,
                success: this._onSuccess,
                error: this._onError
            });
        },

        /*
         * Process ajax success
         * @protected
         * @param {string} response test
         * @param {string} response status
         * @param {Object} The jQuery XMLHttpRequest object returned by $.ajax()
         */
        _onSuccess: function(response, status, jqXHR) {
            if (response.attribute) {
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
            if (this.options.errorUrl) {
                location.href = this.options.errorUrl;
            }
        }
    });
})(jQuery);
