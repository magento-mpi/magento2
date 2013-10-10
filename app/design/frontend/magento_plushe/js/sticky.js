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
        options: {
            container: ''
        },

        /**
         * Bind handlers to scroll event
         * @private
         */
        _create: function() {
            $(window).on('scroll', $.proxy(this._setTop, this));
        },

        /**
         * float Block on windowScroll
         * @private
         */
        _setTop: function() {
            if ((this.element).is(':visible')) {
                var startOffset = this.element.parent().offset().top + parseInt(this.element.css("margin-top")),
                    currentOffset = $(document).scrollTop(),
                    parentHeight = $(this.options.container).height() - parseInt(this.element.css("margin-top")),
                    discrepancyOffset = currentOffset - startOffset;

                if (discrepancyOffset >= 0) {
                    if (discrepancyOffset + this.element.innerHeight() < parentHeight) {
                        this.element.css('top', discrepancyOffset);
                    }
                } else {
                    this.element.css('top', 0);
                }
            }
        }
    });
})(jQuery, window);