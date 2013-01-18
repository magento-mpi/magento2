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
            this._initSortableOptions();
            this._initSortableSelections();
            this._bindCheckboxHandlers();
            this._on({
                'click .remove':  function (event) {
                    $(event.target).closest('.option-box').find('.delete-product-option').trigger('click');
                },
                'click .toggle': function (event) {
                    $(event.target).closest('.option-box').find('.option-header,.form-list,.selection-search').toggle();
                }
            });

            $('#weight_and_type_switcher, label[for=weight_and_type_switcher]').hide();

            var element = this.element;
            $('#product_info_tabs').on('tabsbeforeactivate', function (event, ui) {
                element[$(ui.newPanel).find('#attribute-name-container').length ? 'show' : 'hide']();
            });
        },
        _initSortableOptions: function () {
            this.element.sortable({
                axis: 'y',
                handle: '.entry-edit-head:first',
                items: '.option-box',
                update: this._updateBundleOptionPositions
            });
        },
        _initSortableSelections: function () {
            this.element.find('.option-box .form-list tbody').sortable({
                axis: 'y',
                helper: function(event, ui) {
                    ui.children().each(function() {
                        $(this).width($(this).width());
                    });
                    return ui;
                },
                update: function () {
                    $(this).find('[name^=bundle_selections][name$="[position]"]').each(function (index) {
                        $(this).val(index);
                    });
                }
            })
        },
        _bindCheckboxHandlers: function () {
            this._on({
                'change [data-mage-role=is-required].is-required': function (event) {
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
        _updateBundleOptionPositions: function () {
            $(this).find('[name^=bundle_options][name$="[position]"]').each(function (index) {
                $(this).val(index);
            });
        },
        refreshSortableElements: function () {
            this.element.sortable('refresh');
            this._updateBundleOptionPositions.apply(this.element);
            this._initSortableSelections();
            return this;
        }
    });
});
