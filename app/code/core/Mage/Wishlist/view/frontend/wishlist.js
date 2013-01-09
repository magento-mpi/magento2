/**
 * {license_notice}
 *
 * @category    frontend product msrp
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */

/*jshint browser:true jquery:true*/
(function($, window) {
    "use strict";
    $.widget('mage.wishlist', {
        options: {
            dataAttribute: 'item-id',
            nameFormat: 'qty[{0}]',
            btnRemoveSelector: '.btn-remove',
            qtySelector: '.qty',
            addToCartSelector: '.btn-cart',
            addAllToCartSelector: '.btn-add',
            commentInputType: 'textarea'
        },

        /**
         * Bind handlers to events.
         */
        _create: function() {
            this.element
                .on('click', this.options.addToCartSelector, $.proxy(this._addItemsToCart, this))
                .on('click', this.options.btnRemoveSelector, $.proxy(this._confirmRemoveWishlistItem, this))
                .on('click', this.options.addAllToCartSelector, $.proxy(this._addAllWItemsToCart, this))
                .on('focusin focusout', this.options.commentInputType, $.proxy(this._focusComment, this));
        },

        /**
         * Validate and Redirect.
         * @private
         * @param {string} url
         */
        _validateAndRedirect: function(url) {
            if (this.element.validation({
                errorPlacement: function(error, element) {
                    error.insertAfter(element.next());
                }
            }).valid()) {
                this.element.prop('action', url);
                window.location.href = url;
            }
        },

        /**
         * Add wish list items to cart.
         * @private
         */
        _addItemsToCart: function() {
            $(this.options.addToCartSelector).each($.proxy(function(index, element) {
                if ($(element).data(this.options.dataAttribute)) {
                    var itemId = $(element).data(this.options.dataAttribute),
                        url = this.options.addToCartUrl.replace('%item%', itemId),
                        inputName = $.validator.format(this.options.nameFormat, itemId),
                        inputValue = this.element.find('[name="' + inputName + '"]').val(),
                        separator = (url.indexOf('?') >= 0) ? '&' : '?';
                    url += separator + inputName + '=' + encodeURIComponent(inputValue);
                    this._validateAndRedirect(url);
                    return;
                }
            }, this));
        },

        /**
         * Confirmation window for removing wish list item.
         * @private
         */
        _confirmRemoveWishlistItem: function() {
            return window.confirm(this.options.confirmRemoveMessage);
        },

        /**
         * Add all wish list items to cart
         * @private
         */
        _addAllWItemsToCart: function() {
            var url = this.options.addAllToCartUrl,
                separator = (url.indexOf('?') >= 0) ? '&' : '?';
            this.element.find(this.options.qtySelector).each(function(index, element) {
                url += separator + $(element).prop('name') + '=' + encodeURIComponent($(element).val());
                separator = '&';
            });
            this._validateAndRedirect(url);
        },

        /**
         * Toggle comment string.
         * @private
         * @param {event} e
         */
        _focusComment: function(e) {
            var commentInput = e.currentTarget;
            if (commentInput.value === '' || commentInput.value === this.options.commentString) {
                commentInput.value = commentInput.value === this.options.commentString ?
                    '' : this.options.commentString;
            }
        }
    });
})(jQuery, window);
