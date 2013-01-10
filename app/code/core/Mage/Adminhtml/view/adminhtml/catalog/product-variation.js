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
                handle: '.entry-edit-head',
                update: function () {
                    $(this).find('[name$="[position]"]').each(function (index) {
                        $(this).val(index)
                    })
                }
            });

            var havePriceVariationsCheckboxHandler = function (event) {
                var $this = $(event.target),
                    $block = $this.closest('.entry-edit');
                if ($this.is(':checked')) {
                    $block.addClass('have-price');
                } else {
                    $block.removeClass('have-price');
                    $block.find('.pricing-value').val('');
                }
            };
            var useDefaultCheckboxHandler = function (event) {
                var $this = $(event.target);
                $this.closest('.fieldset-legend').find('.store-label').prop('disabled', $this.is(':checked'));
            };
            var updateGenerateVariationsButtonAvailability = function () {
                var isDisabled = $('#attributes-container .entry-edit:not(:has(input.include:checked))').length > 0
                    || !$('#attributes-container .entry-edit').length;
                $('#generate-variations-button').prop('disabled', isDisabled).toggleClass('disabled', isDisabled);
            };

            this._on({
                'click input.price-variation': havePriceVariationsCheckboxHandler,
                'change input.price-variation': havePriceVariationsCheckboxHandler,
                'click .remove':  function (event) {
                    var $entity = $(event.target).closest('.entry-edit');
                    $('#attribute-' + $entity.find('[name$="[code]"]').val() + '-container select').removeAttr('disabled');
                    $entity.remove();
                },
                'click .toggle': function (event) {
                    $(event.target).parent().next('fieldset').toggle();
                },
                'click input.include': updateGenerateVariationsButtonAvailability,
                'add': function (event, attribute) {
                    $('#attribute-template').tmpl({attribute: attribute}).appendTo($(event.target));
                    $('#attribute-' + attribute.code + '-container select').prop('disabled', true);
                    updateGenerateVariationsButtonAvailability();
                },
                'click .use-default': useDefaultCheckboxHandler,
                'change .use-default': useDefaultCheckboxHandler
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
