/**
 * {license_notice}
 *
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
            var price = 0,
                oldPrice = 0,
                inclTaxPrice = 0,
                exclTaxPrice = 0;
            this.element.find(this.options.linkElement + ':checked').each($.proxy(function(index, element) {
                price += this.options.config.links[$(element).val()].price;
                oldPrice += this.options.config.links[$(element).val()].oldPrice;
                inclTaxPrice += this.options.config.links[$(element).val()].inclTaxPrice;
                exclTaxPrice += this.options.config.links[$(element).val()].exclTaxPrice;
            }, this));
            this.element.trigger('changePrice', {
                'config': 'config',
                'price': {
                    'price': price,
                    'oldPrice': oldPrice,
                    'inclTaxPrice': inclTaxPrice,
                    'exclTaxPrice': exclTaxPrice
                }
            }).trigger('reloadPrice');
        }
    });
})(jQuery);