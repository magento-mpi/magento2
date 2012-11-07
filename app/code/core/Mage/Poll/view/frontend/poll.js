/**
 * {license_notice}
 *
 * @category    frontend poll
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function ($) {
    $.widget('mage.poll', {
        options: {
        },
        _create: function() {
            $(this.options.pollAnswersSelector).decorate('list');
            this.element.on('submit', $.proxy(function() {
                return $(this.options.pollCheckedOption).length > 0;
            }, this));
        }
    });
})(jQuery);
