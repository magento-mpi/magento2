/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function ($) {
    $.widget('mage.addToWishlist', {
        options: {
            bundleInfo: '[id^=bundle-option-]',
            configurableInfo: '.super-attribute-select',
            groupedInfo: '#super-product-table input',
            downloadableInfo: '.options-list input',
            customOptionsInfo: '.product-custom-option'
        },
        _create: function () {
            this.addToWishlist();
        },
        addToWishlist: function () {
            this._on({
                'click [data-action="add-to-wishlist"]': function (event) {
                    var url = $(event.target).closest('a').attr('href'),
                        productInfo = this.options[this.options.productType + 'Info'],
                        additionalData = $(this.options.customOptionsInfo).serialize();
                    if (productInfo !== undefined) {
                        additionalData += $(productInfo).serialize();
                    }
                    $(event.target).closest('a').attr('href', url + (url.indexOf('?') == -1 ? '?' : '&') + additionalData);
                }
            });
        }
    });
})(jQuery);
