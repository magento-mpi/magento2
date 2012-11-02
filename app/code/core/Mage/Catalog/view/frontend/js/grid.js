/**
 * {license_notice}
 *
 * @category    frontend grid
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true expr:true*/
(function ($) {
    $.widget('mage.grid', {
        _create: function () {
            this.options.listId && this.element.decorate('list');
            if (this.options.genericSelector) {
                if (this.options.decoratorParam) {
                    $(this.options.genericSelector).decorate('generic', this.options.decoratorParam);
                }
                else {
                    $(this.options.genericSelector).decorate('generic');
                }
            }
        }
    });
})(jQuery);