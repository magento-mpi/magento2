/**
 * {license_notice}
 *
 * @category    design
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true*/
(function($) {
    'use strict';
    $.widget('vde.quickStyleElement', {
        options: {
            changeEvent: 'change.quickStyleElement',
            focusEvent: 'focus.quickStyleElement',
            saveQuickStylesUrl: null
        },

        _create: function() {
        },

        _init: function() {
            this._bind();
        },

        _bind: function() {
            this.element.on(this.options.focusEvent, $.proxy(this._onFocus, this));
            this.element.on(this.options.changeEvent, $.proxy(this._onChange, this));
        },

        _onFocus: function(event) {
            this.oldValue = $(this.element).val();
        },

        _onChange: function(event) {
            if (this.element.attr('type') == 'checkbox') {
                this.element.trigger('quickStyleElementBeforeChange');
            }

            if (this.oldValue != $(this.element).val()) {
                this.element.trigger('changeTheme', event);
                event.doChange ? this._send() : this._reset();
            }
        },

        _reset: function() {
            $(this.element).closest('form')[0].reset();
            var colorBox = $(this.element).siblings('.color-box');
            if (colorBox) {
                $(colorBox).css({'backgroundColor': $(this.element).val()});
            }
        },

        _send: function() {
            var data = {
                id: this.element.attr('id'),
                value: this.element.val()
            };

            $.ajax({
                type: 'POST',
                url:  this.options.saveQuickStylesUrl,
                data: data,
                dataType: 'json',
                success: $.proxy(function(response) {
                    if (response.error) {
                        alert(response.message);
                    }
                    this.element.trigger('refreshIframe');
                }, this),
                error: function() {
                    alert($.mage.__('Error: unknown error.'));
                }
            });
        }
    });
})(window.jQuery);
