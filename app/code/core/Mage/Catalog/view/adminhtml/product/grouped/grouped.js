/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/

jQuery(function($) {
    $.widget('mage.groupedProduct', {
        _create: function () {
            var widget = this;
            this.$gridDialog = $('#grouped-product-popup').dialog({
                title: 'Select Associated Products',
                autoOpen: false,
                minWidth: 980,
                modal: true,
                resizable: true
            });

            this.options.grid.rowClickCallback = function () {};
            this.options.gridPopup.rowClickCallback = function (grid, event) {
                event.stopPropagation();
                if (!this.rows || !this.rows.length) {
                    return;
                }
                $(event.target).parent().find('td.selected-products input[type="checkbox"]').click();
                return false;
            };
            if (!$.isArray(this.options.gridData) || this.options.gridData.length) {
                this._initGridWithData(this.options.gridData);
            }
            this._displayGridRow(this.options.associatedProductsId);
            this._updatePopupGrid();
            this._updateHiddenField(this.options.associatedProductsId);
            $('#grouped-add-products').on('click', function () {
                widget.$gridDialog.dialog('open');
                return false;
            });
            $('#grouped-products-select').on('click', function () {
                var ids = widget._getSelectedIds();
                widget._updateHiddenField(ids);
                widget._displayGridRow(ids);
                widget.$gridDialog.dialog('close');
                return false;
            });
            this.options.$groupedGrid.on('click', '.product-delete button', function (event) {
                event.preventDefault();
                event.stopPropagation();
                var $this = $(this);
                $this.closest('tr').hide().addClass('ignore-validate');
                widget._updatePopupGrid();
                widget._updateHiddenField(widget._getSelectedIds());
            });
            this.options.$groupedGrid.on('change keyup', 'input[type="text"]', function (event) {
                widget._updateHiddenField(widget._getSelectedIds());
            });
        },

        _updateHiddenField: function (ids) {
            var gridData = {}, widget = this;
            $.each(this.options.$groupedGrid.find('td.selected-products'), function () {
                var $idContainer = $(this),
                    inArray = $.inArray($idContainer.html().trim(), ids) !== -1;
                if (inArray) {
                    var data = {};
                    $.each(widget.options.fieldsToSave, function (k, v) {
                        data[v] = $idContainer.closest('tr').find('input[name="' + v + '"]').val()
                    });
                    gridData[$(this).html().trim()] = data;
                }
            });
            widget.options.$hiddenInput.val(JSON.stringify(gridData));
        },

        _displayGridRow: function (ids) {
            $.each(this.options.$groupedGrid.find('td.selected-products'), function () {
                var $idContainer = $(this),
                    inArray = $.inArray($idContainer.html().trim(), ids) !== -1;
                $idContainer.closest('tr').toggle(inArray).toggleClass('ignore-validate', !inArray);
            });
        },

        _initGridWithData: function (gridData) {
            $.each(this.options.$groupedGrid.find('td.selected-products'), function () {
                var $idContainer = $(this),
                    id = $idContainer.html().trim();
                $.each(gridData[id], function (fieldName, data) {
                    $idContainer.closest('tr').find('input[name="' + fieldName + '"]').val(data);
                });
            });
        },

        _getSelectedIds: function () {
            var ids = [];
            $.each(this.options.$groupedGridPopup.find('.selected-products input[type="checkbox"]:checked'),
                function () {
                    ids.push($(this).val());
                }
            );
            return ids;
        },

        _updatePopupGrid: function () {
            var widget = this;
            $.each(this.options.$groupedGrid.find('td.selected-products'), function () {
                var id = $(this).html().trim();
                widget.options.$groupedGridPopup.find('input[type=checkbox][value="' + id + '"]')
                    .prop({checked: !$(this).closest('tr').hasClass('ignore-validate')});
            });
        }
    });
});
