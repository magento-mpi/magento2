/**
 * {license_notice}
 *
 * @category    frontend product msrp
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */

/*jshint evil:true browser:true jquery:true*/
(function($, window) {
    "use strict";
    $.widget('mage.addWishListToCart', {
        options: {
            dataAttribute: 'item-id',
            nameFormat: 'qty[{0}]',
            wishListFormSelector: '#wishlist-view-form',
            btnRemoveSelector: '.btn-remove',
            qtySelector: '.qty',
            addToCartSelector: '.btn-cart',
            addAllToCartSelector: '.btn-add',
            commentInputType: 'textarea'
        },

        _create: function() {
            $(this.options.wishListFormSelector).on('click', this.options.addToCartSelector, $.proxy(this._addItemsToCart, this)).
                on('click', this.options.btnRemoveSelector, $.proxy(this._confirmRemoveWishlistItem, this)).
                on('click', this.options.addAllToCartSelector, $.proxy(this._addAllWItemsToCart, this)).
                on('focusin focusout', this.options.commentInputType, $.proxy(this._focusComment, this));
        },

        _validateAndRedirect: function(url) {
            if ($(this.options.wishListFormSelector).validation({
                errorPlacement: function(error, element) {
                    error.insertAfter(element.next());
                }
            }).valid()) {
                window.location.href = url;
            }
        },

        _addItemsToCart: function(e) {
            var btn = $(e.currentTarget),
                itemId = btn.data(this.options.dataAttribute),
                url = this.options.addToCartUrl.replace('%item%', itemId),
                inputName = $.validator.format(this.options.nameFormat, itemId),
                inputValue = $(this.options.wishListFormSelector).find('[name="' + inputName + '"]').val(),
                separator = (url.indexOf('?') >= 0) ? '&' : '?';
            url += separator + inputName + '=' + encodeURIComponent(inputValue);

            this._validateAndRedirect(url);
        },

        _confirmRemoveWishlistItem: function() {
            return window.confirm(this.options.confirmRemMsg);
        },

        _addAllWItemsToCart: function() {
            var url = this.options.addAllToCartUrl;
            var separator = (url.indexOf('?') >= 0) ? '&' : '?';
            $(this.options.wishListFormSelector).find(this.options.qtySelector).each(
                function(index, elem) {
                    url += separator + $(elem).prop('name') + '=' + encodeURIComponent($(elem).val());
                    separator = '&';
                }
            );

            this._validateAndRedirect(url);
        },

        _focusComment: function(e) {
            var commentInput = e.currentTarget;
            commentInput.value = commentInput.value === this.options.commentStr ? '' : this.options.commentStr;
        }
    });
})(jQuery, window);
