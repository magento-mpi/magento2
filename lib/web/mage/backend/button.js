/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
(function (factory) {
    if (typeof define === 'function' && define.amd) {
        define([
            "jquery",
            "jquery/ui"
        ], factory);
    } else {
        factory(jQuery);
    }
}(function ($) {
    'use strict';
    
    $.widget('ui.button', $.ui.button, {
        options: {
            eventData: {}
        },

        /**
         * Button creation
         * @protected
         */
        _create: function() {
            if (this.options.event) {
                this.options.target = this.options.target || this.element;
                this._bind();
            }
            this._super();
        },

        /**
         * Bind handler on button click
         * @protected
         */
        _bind: function() {
            this.element
                .off('click.button')
                .on('click.button', $.proxy(function() {
                    $(this.target).trigger(this.event, [this.eventData]);
                }, this.options));
        }
    });
}));
