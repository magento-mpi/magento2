/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
(function($) {
    $.widget("mage.configurableAttributes", {
        //Set up the widget
        _create: function () {
            this.element.sortable({
                axis: "y",
                handle: '.entry-edit-head',
                //containment: "parent",
                update: function (event, ui) {
                    $(this).find('.attribute-position').each(function (index) {
                        $(this).val(index)
                    })
                }
            });
            this.element.on('click change', 'input.price-variation', function (event) {
                var $this = $(this),
                    $block = $this.closest('.entry-edit');

                if ($this.is(':checked')) {
                    $block.addClass('have-price');
                } else {
                    $block.removeClass('have-price');
                    $block.find('.pricing-value').val('');
                }
            });
            this.element.on('click', '.remove', function (event) {
                $(this).closest('.entry-edit').remove();
            });
            this.element.on('click', '.toggle', function (event) {
                $(this).parent().next('fieldset').toggle();
            });
            this.element.on('add', function (event, attribute) {
                $('#attribute-template').tmpl({attribute: attribute}).appendTo($(this));
            });
        }
    });
})(jQuery);
