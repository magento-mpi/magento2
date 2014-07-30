/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true */
define([
    "jquery",
    "jquery/ui"
], function($){

    $.widget('mage.redirectUrl', {
        options: {
            event: 'click',
            url: undefined
        },

        /**
         * This method binds elements found in this widget.
         * @private
         */
        _bind: function() {
            var handlers = {};
            handlers[this.options.event] = '_onEvent';
            this._on(handlers);
        },

        /**
         * This method constructs a new widget.
         * @private
         */
        _create: function() {
            this._bind();
        },

        /**
         * This method set the url for the redirect.
         * @private
         */
        _onEvent: function(){
            if (this.options.url) {
                location.href = this.options.url;
            } else {
                location.href = this.element.val();
            }
        }
    });

});