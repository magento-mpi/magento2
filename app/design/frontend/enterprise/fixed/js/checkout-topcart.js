/**
 * {license_notice}
 *
 * @category    mage product view
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function ($) {
    "use strict";
    $.widget('mage.topCart', {
        options: {
            intervalDuration: 4000
        },

        _create: function(){
            this.element.find(this.options.closeSelector)
                .on('click', $.proxy(this.hide, this));
            this.element.parent()
                .on('mouseleave', $.proxy(this._onMouseleave, this))
                .on('mouseenter', $.proxy(this._stopTimer, this));
            this.element.prev().on('click', $.proxy(function () {
                this.element.slideToggle('slow');
            }, this));
        },

        /**
         * Hide (slide up) the checkout top-cart.
         */
        hide: function(){
            this.element.slideUp('slow', $.proxy(this._stopTimer, this));
        },

        /**
         * Clear (stop) the timer that controls the show/hide of the checkout top-cart.
         * @private
         */
        _stopTimer: function() {
            clearTimeout(this.timer);
        },

        /**
         * Executes when the mouse leaves the top-cart area. Initiates hiding of the top-cart
         * after a set interval duration.
         * @private
         */
        _onMouseleave: function() {
            this._stopTimer();
            this.timer = setTimeout($.proxy(this.hide, this), this.options.intervalDuration);
        }
    });
})(jQuery);
