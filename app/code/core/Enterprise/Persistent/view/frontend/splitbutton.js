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
            splitButton: '.split-button',
            arrowButton: '.change',
            activeClass: 'active'
        },
        _create: function() {
            $(this.options.splitButton + ' ' + this.options.arrowButton).on('click', $.proxy(this.toggleDropDown, this));
            $(document).on('click', $.proxy(this.hideDropDown, this));
        },
        toggleDropDown: function(e) {
            $(e.target).closest(this.options.splitButton).toggleClass(this.options.activeClass);
            return false;
        },
        hideDropDown: function(e) {
            $('.' + this.options.activeClass).removeClass(this.options.activeClass);
        }
    });
})(jQuery);
