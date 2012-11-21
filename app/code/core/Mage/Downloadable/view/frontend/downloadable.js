/**
 * {license_notice}
 *
 * @category    mage downloadable view
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true expr:true*/
(function ($) {
    $.widget('mage.downloadable', {
        _create: function() {
            $(this.options.linkClass).on('change', $.proxy(function() {
                this._reloadPrice();
            }, this));
        },

        /**
         * Reload product price with selected link price included
         * @private
         */
        _reloadPrice: function() {
            var price = 0;
            $(this.options.linkClass + ':checked').each($.proxy(function(index, element) {
                price += this.options.config[$(element).val()];
            }, this));
            this.options.priceOptionInstance.changePrice('config', {'price': price});
            this.options.priceOptionInstance.reloadPrice();
        }
    });
})(jQuery);