/**
 * {license_notice}
 *
 * @category    mage
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function($) {
    $.widget("mage.proxyEvent", {
        /**
         * Proxy creation
         * @protected
         */
        _create: function() {
            $.each(this.options.events || {}, $.proxy(function(eventName, options) {
                if (typeof eventName != 'string') {
                    throw new Error('Event name mast be string type');
                }

                options = $.extend({
                    preventDefault: true,
                    stopPropagation: false,
                    to: 'body'
                }, options);

                this.element.on(eventName, function(event, data) {
                    if (options.preventDefault) {
                        event.preventDefault();
                    }
                    if (options.stopPropagation) {
                        event.stopPropagation();
                    }
                    $(options.to).trigger(options.event || event, data);
                });

            }, this));
        }
    });
})(jQuery);
