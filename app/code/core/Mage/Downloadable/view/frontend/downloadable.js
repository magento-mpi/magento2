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
            this.element.find(this.options.linkElement).on('change', $.proxy(function() {
                this._reloadPrice();
            }, this));
        },

        /**
         * Reload product price with selected link price included
         * @private
         */
        _reloadPrice: function() {
            var price = 0;
            this.element.find(this.options.linkElement + ':checked').each($.proxy(function(index, element) {
                price += this.options.config[$(element).val()];
            }, this));
            this.element.trigger('changePrice', {'config': 'config', 'price': {'price': price} }).trigger('reloadPrice');
        }
    });
})(jQuery);