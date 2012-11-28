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
    "use strict";
    $.widget('ui.button', $.ui.button, {
        /**
         * Button creation
         * @protected
         */
        _create: function() {
            this._processDataAttr();
            this._bind();
            this._super();
        },

        /**
         * Get additional options from data attribute and merge it in this.options
         * @protected
         */
        _processDataAttr: function() {
            var data = this.element.data().widgetButton;
            $.extend(true, this.options, $.type(data) === 'object' ? data : {});
        },

        /**
         * Bind handler on button click
         * @protected
         */
        _bind: function() {
            this.element.on('click', $.proxy(function() {
                $(this.options.related)
                    .trigger(this.options.event, this.options.eventData ? [this.options.eventData] : [{}]);
            }, this));
        }
    });
})(jQuery);
