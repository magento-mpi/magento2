/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function($) {
    'use strict';
    $.widget('mage.groupedProduct', {
        /**
         * Greate widget
         * @private
         */
        _create: function () {
            this.$popup = this.element.find('#grouped_grid_popup');
            this.$grid = this.element.find('[data-role=grouped-product-grid]');
            this.$grid.sortable({
                distance: 8,
                items: 'tbody>tr',
                tolerance: "pointer",
                cancel: 'input, button',
                update: $.proxy(function() {
                    this.element.trigger('resort');
                }, this)
            });

            this.$template = this.element.find('#group-product-template');
            $.each(
                this.$grid.data('products'),
                $.proxy(function(index, product) {
                    this._add(null, product);
                }, this)
            );

            this._on({
                'add': '_add',
                'resort': '_resort',
                'click [data-column=actions] [data-role=delete]': '_remove'
            });

            this._bindDialog();
            this._bindEventHandlers();
            this._updateGridVisibility();
        },

        /**
         * Add product to grouped grid
         * @param event
         * @param product
         * @private
         */
        _add: function(event, product) {
            this.$template.tmpl(product).appendTo(this.$grid.find('tbody'));
        },

        /**
         * Remove product
         * @param event
         * @private
         */
        _remove: function(event) {
            $(event.target).closest('tr').remove();
            this.element.trigger('resort');
            this._updateGridVisibility();
        },

        /**
         * Resort iproducts
         * @private
         */
        _resort: function() {
            this.element.find('[data-role=position]').each($.proxy(function(index, element) {
                $(element).val(index + 1);
            }, this));
        },

        /**
         * Create dialof for show product
         *
         * @private
         */
        _bindDialog: function () {
            var widget = this;
            $('[data-role="add-product-popup"]').dialog({
                title: 'Add Products to Group',
                autoOpen: false,
                minWidth: 980,
                modal: true,
                resizable: true,
                dialogClass: 'grouped',
                buttons: [{
                    id: 'grouped-product-dialog-cancel-button',
                    text: 'Cancel',
                    click: function () {
                        $(this).dialog('close');
                    }
                }, {
                    id: 'grouped-product-dialog-apply-button',
                    text: 'Add Products',
                    'class': 'add',
                    click: function () {
                        widget._addSelected();
                        $(this).dialog('close');
                    }
                }]
            });
        },

        /**
         * Bind event
         * @private
         */
        _bindEventHandlers: function() {

            var widget = this;
            $('[data-role="add-product"]').on('click', function (event) {
                event.preventDefault();
                var skus = widget.$grid.find('[data-role=sku]').map(function(index, element) {
                    return $(element).val()
                }).toArray();
                widget.options.gridPopup.reloadParams = {
                    filter: {'sku': skus ? skus : [0]}
                };
                widget.options.gridPopup.reload(null, function() {
                    $('[data-role=add-product-popup]').dialog('open');
                });

            });
            this.options.gridPopup.rowClickCallback = function(grid, event) {
                event.stopPropagation();
                if (!this.rows || !this.rows.length) {
                    return;
                }
                $(event.target).parent().find('td.col-select input[type=checkbox]').click();
                return false;
            };
        },

        /**
         * Add selected products from grid
         * @private
         */
        _addSelected: function () {
            this.$popup.find('[data-role=row] [data-column=massaction]:has(input:checked)')
                .each($.proxy(function(index, element) {
                    var product = {};
                    product.id = $(element).find('input').val();
                    product.qty = 0;
                    $(element).closest('tr').find('[data-column]').each(function(index, element) {
                        product[$(element).data('column')] = $.trim($(element).text());
                    });

                    this._add(null, product);
                }, this)).toArray();

            this._resort();
            this._updateGridVisibility();
        },

        /**
         * Show or hide message
         * @private
         */
        _updateGridVisibility: function () {
            var showGrid = this.element.find('[data-role="id"]').length > 0;
            this.element.find('.grid-container').toggle(showGrid);
            this.element.find('.no-products-message').toggle(!showGrid);
        }
    });
})(jQuery);
