/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
/*global FORM_KEY*/
/*global bSelection*/
/*global $H*/
(function($) {
    'use strict';
    $.widget('mage.bundleProduct', {
        _create: function () {
            this._initOptionBoxes();
            this._initSortableSelections();
            this._bindCheckboxHandlers();
            this._bindAddSelectionDialog();
            this._hideProductTypeSwitcher();
        },
        _initOptionBoxes: function () {
            this.element.sortable({
                axis: 'y',
                handle: '[data-role=draggable-handle]',
                items: '.option-box',
                update: this._updateOptionBoxPositions,
                tolerance: 'pointer'
            });

            var syncOptionTitle = function (event) {
                var originalValue = $(event.target).attr('data-original-value'),
                    currentValue = $(event.target).val(),
                    optionBoxTitle = $('.title > span', $(event.target).closest('.option-box')),
                    newOptionTitle = $.mage.__('New Option');

                optionBoxTitle.text(currentValue === '' && !originalValue.length ? newOptionTitle : currentValue);
            };
            this._on({
                'change .field-option-title input[name$="[title]"]': syncOptionTitle,
                'keyup .field-option-title input[name$="[title]"]': syncOptionTitle,
                'paste .field-option-title input[name$="[title]"]': syncOptionTitle
            });
        },
        _initSortableSelections: function () {
            this.element.find('.option-box .form-list tbody').sortable({
                axis: 'y',
                handle: '[data-role=draggable-handle]',
                helper: function(event, ui) {
                    ui.children().each(function() {
                        $(this).width($(this).width());
                    });
                    return ui;
                },
                update: this._updateSelectionsPositions,
                tolerance: 'pointer'
            });
        },
        _bindAddSelectionDialog: function () {
            var widget = this;
            this._on({'click .add-selection': function (event) {
                var $optionBox = $(event.target).closest('.option-box'),
                    $selectionGrid = $optionBox.find('.selection-search'),
                    optionIndex = $optionBox.attr('id').replace('bundle_option_', ''),
                    productIds = [],
                    productSkus = [],
                    selectedProductList = {};

                $optionBox.find('[name$="[product_id]"]').each(function () {
                    if (!$(this).closest('tr').find('[name$="[delete]"]').val()) {
                        productIds.push($(this).val());
                        productSkus.push($(this).closest('tr').find('.col-sku').text());
                    }
                });

                bSelection.gridSelection.set(optionIndex, $H({}));
                bSelection.gridRemoval = $H({});
                bSelection.gridSelectedProductSkus = productSkus;
                $selectionGrid.on('change', '.col-id input', function () {//_on can't be used because of grid reloading
                    var tr = $(this).closest('tr');
                    if ($(this).is(':checked')) {
                        selectedProductList[$(this).val()] = {
                            name: $.trim(tr.find('.col-name').html()),
                            sku: $.trim(tr.find('.col-sku').html()),
                            product_id: $(this).val(),
                            option_id: $('bundle_selection_id_' + optionIndex).val(),
                            selection_price_value: 0,
                            selection_qty: 1
                        };
                    } else {
                        delete selectedProductList[$(this).val()];
                    }
                });
                $selectionGrid.dialog({
                    title: $optionBox.find('input[name$="[title]"]').val() === '' ?
                        $.mage.__('Add Products to New Option') :
                        $.mage.__('Add Products to Option "%1"')
                            .replace('%1',($('<div>').text($optionBox.find('input[name$="[title]"]').val()).html())),
                    autoOpen: false,
                    minWidth: 980,
                    'class': 'bundle',
                    modal: true,
                    resizable: true,
                    buttons: [{
                        text: $.mage.__('Cancel'),
                        click: function() {
                            $selectionGrid.dialog('close');
                        }
                    }, {
                        text: $.mage.__('Add Selected Products'),
                        'class': 'add primary',
                        click: function() {
                            $.each(selectedProductList, function() {
                                window.bSelection.addRow(optionIndex, this);
                            });
                            bSelection.gridRemoval.each(
                                function(pair) {
                                    $optionBox.find('.col-sku').filter(function () {
                                        return $.trim($(this).text()) === pair.key; // find row by SKU
                                    }).closest('tr').find('button.delete').trigger('click');
                                }
                            );
                            widget.refreshSortableElements();
                            widget._updateSelectionsPositions.apply(widget.element);
                            $selectionGrid.dialog('close');
                        }
                    }],
                    close: function() {
                        $(this).dialog('destroy');
                    }
                });

                $.ajax({
                    url: bSelection.selectionSearchUrl,
                    dataType: 'html',
                    data: {
                        index: optionIndex,
                        products: productIds,
                        selected_products: productIds,
                        form_key: FORM_KEY
                    },
                    success: function(data) {
                        $selectionGrid.html(data).dialog('open');
                    },
                    context: $('body'),
                    showLoader: true
                });
            }});
        },
        _hideProductTypeSwitcher: function () {
            $('#weight_and_type_switcher, label[for=weight_and_type_switcher]').hide();
        },
        _bindCheckboxHandlers: function () {
            this._on({
                'change .is-required': function (event) {
                    var $this = $(event.target);
                    $this.closest('.option-box').find('[name$="[required]"]').val($this.is(':checked') ? 1 : 0);
                },
                'change .is-user-defined-qty': function (event) {
                    var $this = $(event.target);
                    $this.closest('.qty-box').find('.select').val($this.is(':checked') ? 1 : 0);
                }
            });
            this.element.find('.is-required').each(function () {
                $(this).prop('checked', $(this).closest('.option-box').find('[name$="[required]"]').val() > 0);
            });
            this.element.find('.is-user-defined-qty').each(function () {
                $(this).prop('checked', $(this).closest('.qty-box').find('.select').val() > 0);
            });
        },
        _updateOptionBoxPositions: function () {
            $(this).find('[name^=bundle_options][name$="[position]"]').each(function (index) {
                $(this).val(index);
            });
        },
        _updateSelectionsPositions: function () {
            $(this).find('[name^=bundle_selections][name$="[position]"]').each(function (index) {
                $(this).val(index);
            });
        },
        refreshSortableElements: function () {
            this.element.sortable('refresh');
            this._updateOptionBoxPositions.apply(this.element);
            this._initSortableSelections();
            return this;
        }
    });
})(jQuery);
