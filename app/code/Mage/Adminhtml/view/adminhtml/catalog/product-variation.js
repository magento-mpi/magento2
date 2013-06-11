/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
(function($) {
    $.widget('mage.variationsAttributes', {
        _create: function () {
            this.element.sortable({
                axis: 'y',
                handle: '.draggable-handle',
                tolerance: 'pointer',
                update: function () {
                    $(this).find('[name$="[position]"]').each(function (index) {
                        $(this).val(index);
                    });
                }
            });
            var updateGenerateVariationsButtonAvailability = function () {
                var isDisabled =
                    $('#configurable-attributes-container .entry-edit:not(:has(input.include:checked))').length > 0 ||
                    !$('#configurable-attributes-container .entry-edit').length;
                $('#generate-variations-button').prop('disabled', isDisabled).toggleClass('disabled', isDisabled);
            };

            this._on({
                'menuselect [data-field="is-percent"] [data-role="dropdown-menu"]': function (event, ui) {
                    var parent = $(event.target).closest('[data-field="is-percent"]');
                    parent.find('[data-toggle="dropdown"] span').text(ui.item.text());
                    parent.find('[data-role="is-percent-change"]').val(ui.item.attr('data-value'));
                    parent.find('[data-toggle="dropdown"]').trigger('close.dropdown');
                    $(event.target).find('[data-value]').show();
                    ui.item.hide();
                },
                'click .fieldset-wrapper-title .action-delete':  function (event) {
                    var $entity = $(event.target).closest('.entry-edit');
                    $('#attribute-' + $entity.find('[name$="[code]"]').val() + '-container select').removeAttr('disabled');
                    $entity.remove();
                    updateGenerateVariationsButtonAvailability();
                    event.stopImmediatePropagation();
                },
                'click .toggle': function (event) {
                    $(event.target).parent().next('fieldset').toggle();
                },
                'click input.include': updateGenerateVariationsButtonAvailability,
                'add': function (event, attribute) {
                    $('#attribute-template').tmpl({attribute: attribute}).appendTo($(event.target)).trigger('contentUpdated');
                    $('#attribute-' + attribute.code + '-container select').prop('disabled', true);

                    $('.collapse')
                        .collapsable()
                        .collapse('show');
                    var attributeContent = $('[data-role="variation-attribute-container"] [data-attribute-id="' + attribute.id + '"]');
                    attributeContent.find('[data-toggle=dropdown]').dropdown();
                    attributeContent.find('[data-role="dropdown-menu"]').each(function (index, element) {
                        $(element).trigger('menuselect', {item: $(element).find('[data-value="0"]')});
                    });
                    $('[data-store-label]').useDefault();

                    updateGenerateVariationsButtonAvailability();
                }
            });
            this.element.find('[data-field="is-percent"]').each(function (index, element) {
                var item = {item: $(element).find('[data-value="' + $(element).find('[data-role="is-percent-change"]').val() + '"]')};
                $(element).find('[data-role="dropdown-menu"]').trigger('menuselect', item);
            });
            updateGenerateVariationsButtonAvailability();
        },
        /**
         * Retrieve list of attributes
         *
         * @return {Array}
         */
        getAttributes: function () {
            return $.map(
                $(this.element).find('.entry-edit') || [],
                function (attribute) {
                    var $attribute = $(attribute);
                    return {
                        id: $attribute.find('[name$="[attribute_id]"]').val(),
                        code: $attribute.find('[name$="[code]"]').val(),
                        label: $attribute.find('[name$="[label]"]').val(),
                        position: $attribute.find('[name$="[position]"]').val()
                    };
                }
            );
        }
    });
})(jQuery);
