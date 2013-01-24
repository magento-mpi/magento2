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
    $.widget('mage.actionLink', {
        /**
         * Button creation
         * @protected
         */
        _create: function() {
            this._bind();
        },

        /**
         * Bind handler on button click
         * @protected
         */
        _bind: function() {
            this._on({
                mousedown: function(e){
                    e.stopImmediatePropagation();
                    e.preventDefault();
                },
                mouseup: function(e){
                    e.stopImmediatePropagation();
                    e.preventDefault();
                },
                click: function(e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    $(this.options.related || this.element)
                        .trigger(this.options.event, this.options.eventData ? [this.options.eventData] : [{}]);
                }
            });
        }
    });
})(jQuery);