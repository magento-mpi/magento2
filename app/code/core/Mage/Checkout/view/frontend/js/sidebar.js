/**
 * {license_notice}
 *
 * @category    mage side bar
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
/*global confirm:true*/
(function ($) {
    $.widget('mage.sidebar', {
        options: {
            isRecursive: true
        },
        _create: function() {
            this.element.decorate('list', this.options.isRecursive);
            $(this.options.checkoutButton).on('click', $.proxy(function() {
                location.href = this.options.checkoutUrl;
            }, this));
            $(this.options.removeButton).on('click', $.proxy(function() {
                return confirm(this.options.confirmMessage);
            }, this));
        }
    });
})(jQuery);
