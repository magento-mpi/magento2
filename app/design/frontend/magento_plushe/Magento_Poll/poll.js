/**
 * {license_notice}
 *
 * @category    design
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function ($) {
    $.widget('mage.poll', {
        options: {
        },
        _create: function() {
            this.element.on('submit', $.proxy(function() {
                return $(this.options.pollCheckedOption).length > 0;
            }, this));
        }
    });
})(jQuery);
