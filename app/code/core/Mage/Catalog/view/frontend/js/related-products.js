/*jshint jquery:true*/
(function($) {
    "use strict";
    $.widget('mage.relatedProducts', {
        options: {
            relatedCheckbox: '.related-checkbox',
            relatedProductsCheckFlag: false,
            relatedProductsField: '#related-products-field',
            selectAllMessage: $.mage.__('select all'),
            unselectAllMessage: $.mage.__('unselect all')
        },

        _create: function() {
            this.element.on('click', $.proxy(function(e) { return this._selectAllRelated(e); }, this));
            $(this.options.relatedCheckbox).on('click', $.proxy(this._addRelatedToProduct, this));
        },

        _selectAllRelated: function(e) {
            var innerHTML = this.options.relatedProductsCheckFlag ?
                this.options.selectAllMessage : this.options.unselectAllMessage;
            $(e.target).html(innerHTML);
            $(this.options.relatedCheckbox).attr('checked',
                this.options.relatedProductsCheckFlag = !this.options.relatedProductsCheckFlag);
            this._addRelatedToProduct();
            return false;
        },

        _addRelatedToProduct: function() {
            var checkboxes = $(this.options.relatedCheckbox),
                values = [];
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].checked) {
                    values.push(checkboxes[i].value);
                }
            }
            if ($(this.options.relatedProductsField)) {
                $(this.options.relatedProductsField).val(values.join(','));
            }
        }
    });
})(jQuery);
