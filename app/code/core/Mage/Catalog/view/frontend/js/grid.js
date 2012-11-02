/**
 * {license_notice}
 *
 * @category    frontend grid
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function ($) {
    $.widget('mage.grid', {
        _create : function() {
            if (this.options.listId) {
                $(this.options.listId).decorate('list');
            }
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