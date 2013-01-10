/**
 * {license_notice}
 *
 * @category    frontend bundle product float
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true expr:true*/
(function($, window) {
    $.widget('mage.float', {
        options: {
            productOptionsSelector: '#product-options-wrapper'
        },

        /**
         * Bind handlers to scroll event
         * @private
         */
        _create: function() {
            $(window).on('scroll', $.proxy(this._setTop, this));
        },

        /**
         * float bundleSummary on windowScroll
         * @private
         */
        _setTop: function() {
            if ((this.element).is(':visible')) {
                var starTop = $(this.options.productOptionsSelector).offset().top,
                    offset = $(document).scrollTop(),
                    maxTop = this.element.parent().offset().top;
                if (!this.options.top) {
                    this.options.top = this.element.position().top;
                    this.element.css('top', this.options.top);
                }

                if (starTop > offset) {
                    return false;
                }

                if (offset < this.options.top) {
                    offset = this.options.top;
                }

                var allowedTop = this.options.top + offset - starTop;

                if (allowedTop < maxTop) {
                    this.element.css('top', allowedTop);
                }
            }
        }
    });
})(jQuery, window);

