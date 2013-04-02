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
        _create: function () {
            this.$grid = this.element.find('#grouped_grid');
            this.$popup = this.element.find('#grouped_grid_popup');

            this._bindDialog();
            this._bindEventHandlers();
            if (!$.isArray(this.options.gridData) || this.options.gridData.length) {
                this._initGridWithData(this.options.gridData);
            }
            this._displayGridRow(this.options.associatedProductIds);
            this._updatePopupGrid();
            this._updateHiddenField(this.options.associatedProductIds);
            this._sortGridByPosition();
        },

        _bindDialog: function () {
            var widget = this;
            $('#grouped-product-popup').dialog({
                title: 'Add Products to Group',
                autoOpen: false,
                minWidth: 980,
                modal: true,
                resizable: true,
                buttons: [{
                    id: 'grouped-product-dialog-cancel-button',
                    text: 'Cancel',
                    click: function () {
                        widget._updatePopupGrid();
                        $(this).dialog('close');
                    }
                }, {
                    id: 'grouped-product-dialog-apply-button',
                    text: 'Add Products',
                    'class': 'add',
                    click: function () {
                        var ids = $.merge(
                            widget._getSelectedIds(),
                            $.parseJSON(widget.options.$hiddenInput.attr('data-ids'))
                        );
                        widget._displayGridRow(ids);
                        widget._updateHiddenField(ids);
                        $(this).dialog('close');
                    }
                }]
            });
        },

        _bindEventHandlers: function () {
            var widget = this;
            $('#grouped-add-products').on('click', function () {
                var ids = widget.options.$hiddenInput.attr('data-ids');
                widget.options.gridPopup.reloadParams = {
                    filter: {'in_products': $.parseJSON(ids.length ? ids : [0])}
                };
                widget.options.gridPopup.reload(null, function(){
                    $('#grouped-product-popup').dialog('open');
                });
                return false;
            });
            this.$grid.on('click', '.grouped-product-delete button', function () {
                $(this).closest('tr').hide().addClass('ignore-validate');
                widget._updatePopupGrid();
                widget._updateHiddenField(widget._getSelectedIds());
                widget._updateGridVisibility();
            });
            this.$grid.on('change keyup', 'input[type="text"]', function () {
                widget._updateHiddenField(widget._getSelectedIds());
            });
            this.options.grid.rowClickCallback = function () {};
            this.options.gridPopup.rowClickCallback = function (grid, event) {
                event.stopPropagation();
                if (!this.rows || !this.rows.length) {
                    return;
                }
                $(event.target).parent().find('td.col-select input[type="checkbox"]').click();
                return false;
            };
        },

        updateRowsPositions: function () {
            $.each(this.$grid.find('input[name="position"]'), function (index) {
                $(this).val(index);
            });
            this._updateHiddenField(this._getSelectedIds());
        },

        _updateHiddenField: function (ids) {
            var gridData = {}, widget = this;
            $.each(this.$grid.find('input[name="entity_id"]'), function () {
                var $idContainer = $(this),
                    inArray = $.inArray($idContainer.val(), ids) !== -1;
                if (inArray) {
                    var data = {};
                    $.each(widget.options.fieldsToSave, function (k, v) {
                        data[v] = $idContainer.closest('tr').find('input[name="' + v + '"]').val();
                    });
                    gridData[$idContainer.val()] = data;
                }
            });
            widget.options.$hiddenInput.attr('data-ids', JSON.stringify(ids));
            widget.options.$hiddenInput.val(JSON.stringify(gridData));
        },

        _displayGridRow: function (ids) {
            var displayRows = false;
            $.each(this.$grid.find('input[name="entity_id"]'), function () {
                var $idContainer = $(this),
                    inArray = $.inArray($idContainer.val(), ids) !== -1;
                $idContainer.closest('tr').toggle(inArray).toggleClass('ignore-validate', !inArray);
                if (inArray) {
                    displayRows = true;
                }
            });
            this._updateGridVisibility(displayRows);
        },

        _initGridWithData: function (gridData) {
            $.each(this.$grid.find('input[name="entity_id"]'), function () {
                var $idContainer = $(this),
                    id = $idContainer.val();
                if (!gridData[id]) {
                    return true;
                }
                $.each(gridData[id], function (fieldName, data) {
                    $idContainer.closest('tr').find('input[name="' + fieldName + '"]').val(data);
                });
            });
        },

        _getSelectedIds: function () {
            var ids = [];
            $.each(this.$popup.find('td.col-select input[type="checkbox"]:checked'),
                function () {
                    ids.push($(this).val());
                }
            );
            return ids;
        },

        _updatePopupGrid: function () {
            var $popup = this.$popup;
            $.each(this.$grid.find('input[name="entity_id"]'), function () {
                var id = $(this).val();
                $popup.find('input[type=checkbox][value="' + id + '"]')
                    .prop({checked: !$(this).closest('tr').hasClass('ignore-validate')});
            });
        },

        _sortGridByPosition: function () {
            var rows = this.$grid.find('tbody tr');
            rows.sort(function (a, b) {
                var valueA = $(a).find('input[name="position"]').val(),
                    valueB = $(b).find('input[name="position"]').val();
                return (valueA < valueB) ? -1 : (valueA > valueB) ? 1 : 0;
            });
            this.$grid.find('tbody').html(rows);
        },

        _updateGridVisibility: function (showGrid) {
            showGrid = showGrid || this.element.find('#grouped_grid_table tbody tr:visible').length > 0;
            this.element.find('.grid-wrapper').toggle(showGrid);
            this.element.find('.no-products-message').toggle(!showGrid);
        }
    });
})(jQuery);
