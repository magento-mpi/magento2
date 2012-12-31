/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
(function($) {
    $.widget("mage.variationsAttributes", {
        //Set up the widget
        _create: function () {
            this.element.sortable({
                axis: 'y',
                handle: '.entry-edit-head',
                update: function () {
                    $(this).find('.attribute-position').each(function (index) {
                        $(this).val(index)
                    })
                }
            });
            this.element.on('click change', 'input.price-variation', function () {
                var $this = $(this),
                    $block = $this.closest('.entry-edit');

                if ($this.is(':checked')) {
                    $block.addClass('have-price');
                } else {
                    $block.removeClass('have-price');
                    $block.find('.pricing-value').val('');
                }
            });
            this.element.on('click', '.remove', function () {
                var $entity = $(this).closest('.entry-edit');
                $('#attribute-' + $entity.find('.attribute-code').val() + '-container select').removeAttr('disabled');
                $entity.remove();
            });
            this.element.on('click', '.toggle', function () {
                $(this).parent().next('fieldset').toggle();
            });
            this.element.on('add', function (event, attribute) {
                $('#attribute-template').tmpl({attribute: attribute}).appendTo($(this));
                $('#attribute-' + attribute.code + '-container select').attr('disabled', true);
            });
            this.element.on('click change', '.use-default', function () {
                $(this).closest('.fieldset-legend').find('.store-label').prop('disabled', $(this).is(':checked'));
            });
        }
    });
})(jQuery);
