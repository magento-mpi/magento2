/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Persistent
 * @copyright   {copyright}
 * @license     {license_link}
 */

/*jshint browser:true jquery:true*/
(function($, undefined) {
    "use strict";
    $.widget('mage.splitButton', {
        options: {
            wishlistSplitButton:'.split-button .change'
        },
        _create: function() {
            $(this.options.wishlistSplitButton).each(
                $.proxy(function(key, value) {
                    var element = $(value);
                    element.on('click', $.proxy(this.toggleDropDown, this));
                }, this)
            );
        },
        toggleDropDown: function(e) {
            var element = $(e.target),
                parent = element.closest('.split-button');
            parent.toggleClass('active');
        }
    });
})(jQuery);
