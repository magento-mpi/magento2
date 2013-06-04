/**
 * {license_notice}
 *
 * @category    EE
 * @package     EE_refrence
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function($, window) {
    $.widget('mage.sticky', {
        /**
         * Bind handlers to scroll event
         * @private
         */
        _create: function() {
            this.container = this.element.parents('.cart.container');
            this.topSpacing = this.container.offset().top;
            this.containerHeight = this.container.outerHeight();
            $(window).on('scroll resize', $.proxy(this._setTop, this))
                .trigger('scroll');
        },

        /**
         * float summary on windowScroll
         * @private
         */
        _setTop: function() {
            if ((this.element).is(':visible')) {
                var scrollTop = $(window).scrollTop();

                if (scrollTop < this.topSpacing) {
                    this.element.removeClass('bottom fixed');
                } else if (scrollTop + this.element.outerHeight() > this.topSpacing + this.containerHeight) {
                    this.element.removeClass('fixed').addClass('bottom');
                } else {
                    this.element.removeClass('bottom').addClass('fixed');
                }
            }
        }
    });
})(jQuery, window);