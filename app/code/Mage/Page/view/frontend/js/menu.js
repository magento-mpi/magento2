/**
 * {license_notice}
 *
 * @category    frontend home menu
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true */
(function ($) {
    $.widget('mage.menu', {
        options: {
            showDelay: 100,
            hideDelay: 100
        },

        _create: function() {
            this.element.hover($.proxy(function () {
                $(this.element).addClass('over');
                this._show(this.element.children('ul'));
            }, this), $.proxy(function () {
                $(this.element).removeClass('over');
                this._hide(this.element.children('ul'));
            }, this));
        },

        /**
         * Show sub menu by adding shown-sub class
         * @private
         * @param subElement
         */
        _show: function(subElement) {
            if (subElement.data('hideTimeId')) {
                clearTimeout(subElement.data('hideTimeId'));
            }
            subElement.data('showTimeId', setTimeout(function () {
                subElement.addClass('shown-sub');
            }), this.options.showDelay);
        },

        /**
         * Hide sub menu by removing shown-sub class
         * @private
         * @param subElement
         */
        _hide: function(subElement) {
            if (subElement.data('showTimeId')) {
                clearTimeout(subElement.data('showTimeId'));
            }
            subElement.data('hideTimeId', setTimeout(function () {
                subElement.removeClass('shown-sub');
            }), this.options.hideDelay);
        }
    });
})(jQuery);
