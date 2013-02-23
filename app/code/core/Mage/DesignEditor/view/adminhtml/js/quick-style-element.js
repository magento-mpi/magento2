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
            saveQuickStylesUrl: null
        },

        _create: function() {
        },

        _init: function() {
            this._bind();
        },

        _bind: function() {
            this.element.on(this.options.changeEvent, $.proxy(this._onChange, this));
        },
        _onChange: function() {
            this._send();
        },
        _send: function() {
            console.log('_send');

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
