/**
 *
 * @license     {}
 */
(function($) {
    $.widget("mage.form", {
        options: {
            actions: {
                saveAndContinueEdit: {
                    template: '${base}{{each(key, value) args}}${key}/${value}/{{/each}}',
                    args: {'back': 'edit'}
                }
            }
        },
        /**
         * Form creation
         * @protected
         */
        _create: function() {
            $.each(this.options.actions, function(i, v){
                $.template(i, v.template);
            })
            this._bind();
        },
        _bind: function(){
            this.element.on('save saveAndContinueEdit', $.proxy(this._submit, this));
        },
        _getActionUrl: function(action, data){
            var actions = this.options.actions;
            if (actions[action]) {
                return $.tmpl(action, $.extend({
                    base: this.element.attr('action'),
                    args: data || action.args || {}
                }));
            }
            return false;
        },
        _submit: function(e, data) {
            var url = this._getActionUrl(e.type, data);
            if (url) {
                this.element.attr('action', url);
            }
            this.element.triggerHandler('submit');
        }
    });
})(jQuery);




(function($) {
    $.widget("mage.formOld", {
        options: {
            submitUrl: false,
            messagesId: 'messages'
        },
        /**
         * Form creation
         * @protected
         */
        _create: function() {
            /*
             * Initialisation of prototype Validation, will be replaced with jQuery validate plug-in in future.
             */
            this.validator = new Validation(this.element.attr('id'), {
                onElementValidate : $.proxy(this.checkErrors, this)
            });
            /*
             * Temporary functionality exists in order to keep backward compatibility,
             *     will be removed when the form is completely migrate to jQuery.
             * Updating property "validator" in "varienForm" class
             */
            this._change('validator', this.validator);
            this.errorSections = new Hash();
            /*
             * Temporary functionality exists in order to keep backward compatibility,
             *     will be removed when the form is completely migrate to jQuery.
             * Updating property "errorSections" in "varienForm" class
             */
            this._change('errorSections', this.errorSections);
            this.element.on('submit', $.proxy(this.submit, this));
        },
        /*
         * Temporary method exists in order to keep backward compatibility,
         *     will be removed when the form is completely migrate to jQuery.
         * Triggering event "fieldIsChanged" to update fields in temporary additional layer "varienForm".
         * @protected
         * @param {string} property key
         * @param {string} property value
         */
        _change: function(key, value) {
            this.element.triggerHandler('fieldIsChanged.form', [key, value]);
        },
        /*
         * Highlight element if element not valid
         * @param {boolean} validation result
         * @param {Element} DOM-element
         */
        checkErrors: function(result, elm) {
            elm.setHasError(!result, this);
        },
        /*
         * Validate form
         * @param {boolean} if true validate only on client side
         */
        validate: function(frontendOnly) {
            if (this.validator && this.validator.validate()) {
                if (!frontendOnly && this.options.validationUrl){
                    this._validate();
                }
                return true;
            }
            return false;
        },
        /*
         * Validate form on server side via ajax
         * @protected
         */
        _validate: function() {
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
            /*
             * Temporary functionality exists in order to keep backward compatibility,
             *     will be removed when the form is completely migrate to jQuery.
             */
            if (typeof varienGlobalEvents != undefined) {
                varienGlobalEvents.fireEvent('formValidateAjaxComplete', jqXHR);
            }
            var response = $.parseJSON(responseText);
            if (response.error) {
                var messages = $('#' + this.options.messagesId);
                if (messages.size()) {
                    messages.html(response.message);
                }
            }
            else{
                this._submit();
            }
        },
        /*
         * Process ajax error
         * @protected
         */
        _onError: function() {
            location.href = BASE_URL;
        },
        /*
         * Validate form before submiting
         * @param {string} URL for ajax request
         */
        submit: function() {
            /*
             * Temporary functionality exists in order to keep backward compatibility,
             *     will be removed when the form is completely migrate to jQuery.
             */
            var url;
            if(arguments.length > 1 && $.type(arguments[1]) === 'string') {
                url = arguments[1];
            }
            if (typeof varienGlobalEvents != undefined) {
                varienGlobalEvents.fireEvent('formSubmit', this.element.attr('id'));
            }
            /*
             * Temporary functionality exists in order to keep backward compatibility,
             *     will be removed when the form is completely migrate to jQuery.
             */
            this.errorSections = new Hash();
            this._change('errorSections', this.errorSections);
            this.canShowError = true;
            this._change('canShowError', this.canShowError);
            this.options.submitUrl = url;
            this._change('submitUrl', this.submitUrl);
            if (this.validator && this.validator.validate()) {
                if (this.options.validationUrl) {
                    this._validate();
                } else {
                    this._submit();
                }
                return true;
            }
            return false;
        },
        /*
         * Submiting the form
         * @protected
         */
        _submit: function() {
            if (this.options.submitUrl) {
                this.element.attr('action', this.options.submitUrl);
            }
            this.element[0].submit();
        }

    });

    $.widget('ui.button', $.ui.button, {
        _create: function(){
            var data = this.element.data().widgetButton;
            if($.type(data) === 'object') {
                this.element.on('click', function(){
                    $(data.related).trigger(data.event);
                })
            }
            this._super("_create");
        }
    })

})(jQuery);
