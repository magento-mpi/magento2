/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
jQuery(function($) {
    $.widget('mage.bundleProduct', {
        _create: function () {
            this._initOptionBoxes();
            this._initSortableSelections();
            this._bindCheckboxHandlers();
            this._bindAddSelectionDialog();
            this._hideProductTypeSwitcher();
            this._bindPanelVisibilityToggler();
        },
        _initOptionBoxes: function () {
            this.element.sortable({
                axis: 'y',
                handle: '.entry-edit-head > .ui-icon-grip-dotted-vertical',
                items: '.option-box',
                update: this._updateOptionBoxPositions,
                tolerance: 'pointer'
            });

            this._on({
                'click .remove':  function (event) {
                    $(event.target).closest('.option-box').find('.delete-product-option').trigger('click');
                },
                'click .toggle': function (event) {
                    $(event.target).closest('.option-box').find('.option-header,.form-list,.selection-search').toggle();
                },
                'keyup .option-box input[name$="[title]"]': function (event) {
                    $(event.target).closest('.option-box').find('.head-edit-form').text($(event.target).val());
                }
            });
        },
        _initSortableSelections: function () {
            this.element.find('.option-box .form-list tbody').sortable({
                axis: 'y',
                handle: '.ui-icon-grip-dotted-vertical',
                helper: function(event, ui) {
                    ui.children().each(function() {
                        $(this).width($(this).width());
                    });
                    return ui;
                },
                update: this._updateSelectionsPositions,
                tolerance: 'pointer'
            });
            this.element.find('.option-box').each(function () {
                $(this).find('.add-selection').appendTo($(this));
            });
        },
        _bindAddSelectionDialog: function () {
            var self = this;
            this._on({'click .add-selection': function (event) {
                var $optionBox = $(event.target).closest('.option-box'),
                    $selectionGrid = $optionBox.find('.selection-search'),
                    optionIndex = $optionBox.attr('id').replace('bundle_option_', ''),
                    productIds = $optionBox.find('[name$="[product_id]"]').map(function () {
                        return $(this).val();
                    }).get();

                bSelection.gridSelection.set(optionIndex, $H({}));
                $selectionGrid.dialog({
                    title: 'Select Products to Add',
                    autoOpen: false,
                    minWidth: 980,
                    modal: true,
                    resizable: true,
                    buttons: [{
                        text: 'Cancel',
                        click: function() {
                            $selectionGrid.dialog('close');
                        }
                    }, {
                        text: 'Add Selected Product(s) to Option',
                        'class': 'add',
                        click: function() {
                            bSelection.gridSelection.get(optionIndex).each(
                                function(pair) {
                                    bSelection.addRow(optionIndex, {
                                        name: pair.value.get('name'),
                                        selection_price_value: 0,
                                        selection_qty: pair.value.get('qty') == '' ? 1 : pair.value.get('qty'),
                                        sku: pair.value.get('sku'),
                                        product_id: pair.key,
                                        option_id: $('bundle_selection_id_' + optionIndex).val()
                                    });
                                }
                            );

                            self.refreshSortableElements();
                            self._updateSelectionsPositions.apply(self.element);
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
                    data: {index: optionIndex, 'products[]': productIds, form_key: FORM_KEY},
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
        _bindPanelVisibilityToggler: function () {
            var element = this.element;
            this._on('#product_info_tabs', {
                tabsbeforeactivate: function (event, ui) {
                    element[$(ui.newPanel).find('#attribute-name-container').length ? 'show' : 'hide']();
                }
            });
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
});
