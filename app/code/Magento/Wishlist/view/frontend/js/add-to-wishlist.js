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
            this._bind();
        },
        _bind: function() {
            var changeCustomOption = 'change ' + this.options.customOptionsInfo,
                changeProductInfo = 'change ' + this.options[this.options.productType + 'Info'],
                events = {};
            events[changeCustomOption] = '_updateWishlistData';
            events[changeProductInfo] = '_updateWishlistData';
            this._on(events);
        },
        _updateWishlistData: function(event) {
            var dataToAdd = {};
            dataToAdd[$(event.currentTarget).attr('name')] = $(event.currentTarget).val();
            $('[data-action="add-to-wishlist"]').each(function(index, element) {
                var params = $(element).data('post');
                params.data = $.extend({}, params.data, dataToAdd);
                $(element).data('post', params);
            });
        }
    });
})(jQuery);