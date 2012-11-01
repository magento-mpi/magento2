/**
 *
 * @license     {}
 */

(function($) {
    $.widget("mage.validation", $.mage.validation, {
        options: {
            ignore: 'form form input, form form select, form form textarea'
        }
    });

    $.widget("mage.form", $.mage.form, {
        options: {
            handlersData: {
                preview: {
                    target: '_blank'
                },
                saveAndPublish: {
                    action: {
                        args: {back: 'publish'}
                    }
                }
            }
        },

        /**
         * Process preview action before form submit
         * @param {string}
         * @param {Object}
         * @protected
         */
        _beforeSubmit: function(handlerName, data) {
            if (handlerName === 'preview' && this.options.handlersData[handlerName].action) {
                this.element.prop(this._processData(this.options.handlersData[handlerName]));
            } else {
                this._superApply(arguments);
            }
        }
    });
})(jQuery);
