/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true*/
(function ($) {
    $.widget('vde.dialog', $.ui.dialog, {
        options: {
            text: {
                selector: '.confirm_message'
            },
            messages: {
                selector: '.messages'
            }
        },

        /**
         * Dialog creation
         * @protected
         */
        _create: function() {
            this._superApply(arguments);
            this.messages = {
                element: this.element.find(this.options.messages.selector),
                clear: function() {
                    this.element.html('');
                },
                add: function(message) {
                    if (message) {
                        this.element.append(message);
                    }
                },
                set: function(message) {
                    this.element.html(message);
                }
            };
            this.text = {
                element: this.element.find(this.options.text.selector),
                set: function(text) {
                    this.element.html(text);
                }
            };
            this.title = {
                dialog: this,
                set: function(title) {
                    this.dialog._setOption('title', title);
                }
            }
        },

        /**
         * Set main params of confirmation dialog
         *
         * @param {string} title
         * @param {string} text
         * @param {Array.<Object>|Object} buttons
         */
        set: function (title, text, buttons) {
            title = $.mage.__(title);
            text = $.mage.__(text);

            this.text.set(text);
            this.title.set(title);
            this.setButtons(buttons);
        },

        setButtons: function(buttons) {
            if (buttons == undefined) {
                buttons = [];
            } else {
                if ($.type(buttons) !== 'array') {
                    buttons = [buttons];
                }
                buttons.each(function(button){
                    button.text = $.mage.__(button.text)
                });
            }

            if (buttons.length<=1) {
                buttons.push({
                    text: $.mage.__('Close'),
                    click: function() {
                        $(this).dialog('close');
                    },
                    'class': 'action-close'
                });
            }

            this._setOption('buttons', buttons);
        }
    });
})(jQuery);
