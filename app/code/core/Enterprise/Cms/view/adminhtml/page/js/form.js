/**
 * {license_notice}
 *
 * @category    design
 * @package     default_default
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true*/
(function($) {
    "use strict";
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
        _beforeSubmit: function(handlerName) {
            if (handlerName === 'preview' && this.options.handlersData[handlerName].action) {
                this.element.prop(this._processData(this.options.handlersData[handlerName]));
            } else {
                this._superApply(arguments);
            }
        }
    });
})(jQuery);

