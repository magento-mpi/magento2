/**
 * {license_notice}
 *
 * @category    mage checkout
 * @package     accordion
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true browser:true*/
(function($) {
    'use strict';
    // mage.accordion base functionality
    $.widget('mage.accordion', $.ui.accordion, {
        options: {
            heightStyle: 'content',
            animate: false,
            beforeActivate: function(e, ui) {
                // Make sure sections below current are not clickable and sections above are clickable
                var newPanelParent = $(ui.newPanel).parent();
                if (!newPanelParent.hasClass('allow')) {
                    return false;
                }
                newPanelParent.addClass('active allow').prevAll().addClass('allow');
                newPanelParent.nextAll().removeClass('allow');
                $(ui.oldPanel).parent().removeClass('active');
            }
        },

        /**
         * Accordion creation
         * @protected
         */
        _create: function() {
            // Custom to enable section
            this.element.on('enableSection', function(event, data) {
                $(data.selector).addClass('allow').find('h2').trigger('click');
            });
            this._super();
            $(this.options.activeSelector).addClass('allow active').find('h2').trigger('click');
        }
    });
})(jQuery);
