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
            productOptionsSelector: ''
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
                var startOffset = this.element.parent().offset().top + parseInt(this.element.css("margin-top")),
                    currentOffset = $(document).scrollTop(),
                    parentHeight = $(this.options.productOptionsSelector).height() - parseInt(this.element.css("margin-top")),
                    elHeight = this.element.innerHeight(),
                    discrepancyOffset = currentOffset - startOffset;

                if (discrepancyOffset >= 0) {
                    if (discrepancyOffset + elHeight < parentHeight) {
                        this.element.css('top', discrepancyOffset);
                    }
                } else {
                    this.element.css('top', 0);
                }
            }
        }
    });
})(jQuery, window);

