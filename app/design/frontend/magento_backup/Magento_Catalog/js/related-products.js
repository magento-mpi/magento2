/**
 * {license_notice}
 *
 * @category    frontend related products
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true*/
(function($) {
    "use strict";
    $.widget('mage.relatedProducts', {
        options: {
            relatedCheckbox: '.related-checkbox', // Class name for a related product's input checkbox.
            relatedProductsCheckFlag: false, // Related products checkboxes are initially unchecked.
            relatedProductsField: '#related-products-field', // Hidden input field that stores related products.
            selectAllMessage: $.mage.__('select all'),
            unselectAllMessage: $.mage.__('unselect all')
        },

        /**
         * Bind events to the appropriate handlers.
         * @private
         */
        _create: function() {
            this.element.on('click', $.proxy(this._selectAllRelated, this));
            $(this.options.relatedCheckbox).on('click', $.proxy(this._addRelatedToProduct, this));
        },

        /**
         * This method either checks all checkboxes for a product's set of related products (select all)
         * or unchecks them (unselect all).
         * @private
         * @param e - Click event on either the "select all" link or the "unselect all" link.
         * @return {Boolean} - Prevent default event action and event propagation.
         */
        _selectAllRelated: function(e) {
            var innerHTML = this.options.relatedProductsCheckFlag ?
                this.options.selectAllMessage : this.options.unselectAllMessage;
            $(e.target).html(innerHTML);
            $(this.options.relatedCheckbox).attr('checked',
                this.options.relatedProductsCheckFlag = !this.options.relatedProductsCheckFlag);
            this._addRelatedToProduct();
            return false;
        },

        /**
         * This method iterates through each checkbox for all related products and collects only those products
         * whose checkbox has been checked. The selected related products are stored in a hidden input field.
         * @private
         */
        _addRelatedToProduct: function() {
            $(this.options.relatedProductsField).val(
                $(this.options.relatedCheckbox + ':checked').map(function() {
                    return this.value;
                }).get().join(',')
            );
        }
    });
})(jQuery);
