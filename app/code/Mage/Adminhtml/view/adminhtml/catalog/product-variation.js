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
                    $('#attribute-template').tmpl({attribute: attribute}).appendTo($(event.target));
                    $('#attribute-' + attribute.code + '-container select').prop('disabled', true);

                    $('.collapse')
                        .collapsable()
                        .collapse('show');

                    $('[data-store-label]').useDefault();

                    updateGenerateVariationsButtonAvailability();
                }
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
