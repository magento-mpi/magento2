/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true browser:true*/
/*global BASE_URL:true*/
(function (factory) {
    if (typeof define === 'function' && define.amd) {
        define([
            "jquery",
            "jquery/ui",
            "jquery/validate",
            "mage/translate",
            "mage/validation"
        ], factory);
    } else {
        factory(jQuery);
    }
}(function ($) {
    $.extend(true, $.validator.prototype, {
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

    $.extend($.fn, {
        /**
         * ValidationDelegate overridden for those cases where the form is located in another form,
         *     to avoid not correct working of validate plug-in
         * @override
         * @param {string} delegate - selector, if event target matched against this selector,
         *     then event will be delegated
         * @param {string} type - event type
         * @param {function} handler - event handler
         * @return {Element}
         */
        validateDelegate: function (delegate, type, handler) {
            return this.on(type, $.proxy(function (event) {
                var target = $(event.target);
                var form = target[0].form;
                if(form && $(form).is(this) && $.data(form, "validator") && target.is(delegate)) {
                    return handler.apply(target, arguments);
                }
            }, this));
        }
    });

    $.widget("mage.validation", $.mage.validation, {
        options: {
            messagesId: 'messages',
            ignore: ':disabled, .ignore-validate, .no-display.template, ' +
                ':disabled input, .ignore-validate input, .no-display.template input, ' +
                ':disabled select, .ignore-validate select, .no-display.template select, ' +
                ':disabled textarea, .ignore-validate textarea, .no-display.template textarea',
            errorElement: 'label',
            errorUrl: typeof BASE_URL !== 'undefined' ? BASE_URL : null,
            highlight: function(element) {
                if ($.validator.defaults.highlight && $.isFunction($.validator.defaults.highlight)) {
                    $.validator.defaults.highlight.apply(this, arguments);
                }
                $(element).trigger('highlight.validate');
            },
            unhighlight: function(element) {
                if ($.validator.defaults.unhighlight && $.isFunction($.validator.defaults.unhighlight)) {
                    $.validator.defaults.unhighlight.apply(this, arguments);
                }
                $(element).trigger('unhighlight.validate');
            }
        },

        /**
         * Validation creation
         * @protected
         */
        _create: function() {
            if (!this.options.submitHandler && $.type(this.options.submitHandler) !== 'function') {
                if (!this.options.frontendOnly && this.options.validationUrl) {
                    this.options.submitHandler = $.proxy(this._ajaxValidate, this);
                } else {
                    this.options.submitHandler = $.proxy(this._submit, this);
                }
            }
            this.element.on('resetElement', function(e) {$(e.target).rules('remove');});
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
                context: $('body'),
                success: $.proxy(this._onSuccess, this),
                error: $.proxy(this._onError, this),
                showLoader: true,
                dontHide: true
            });
        },

        /*
         * Process ajax success
         * @protected
         * @param {Object} JSON-response
         * @param {string} response status
         * @param {Object} The jQuery XMLHttpRequest object returned by $.ajax()
         */
        _onSuccess: function(response) {
            var attributes = response.attributes || {};
            if (response.attribute) {
                attributes[response.attribute] = response.message;
            }
            for (var attributeCode in attributes) {
                if (attributes.hasOwnProperty(attributeCode)) {
                    $('#' + attributeCode)
                        .addClass('validate-ajax-error')
                        .data('msg-validate-ajax-error', attributes[attributeCode]);
                    this.validate.element("#" + attributeCode);
                }
            }
            if (!response.error) {
                this._submit();
            }
        },

        /**
         * Submitting a form
         * @private
         */
        _submit: function() {
            this.element[0].submit();
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
}));
