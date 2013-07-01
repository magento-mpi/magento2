/**
 * {license_notice}
 *
 * @category    mage
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true*/
(function($) {
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
            this.options.target = this.options.target || this.element;
            this._bind();
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
})(jQuery);
