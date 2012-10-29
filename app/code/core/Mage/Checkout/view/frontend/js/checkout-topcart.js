/**
 * {license_notice}
 *
 * @category    mage product view
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function ($) {
    $.widget('mage.topCart', {
        options: {
            intervalDuration: 4000
        },
        _create: function(){
            this.closeButton = this.element.find(this.options.closeSelector);
            this.element.parent()
                .on('mouseleave', $.proxy(this._onMouseleave, this))
                .on('mouseenter', $.proxy(function() {
                clearTimeout(this.timer);
            }, this));
            this.element.prev().on('click', $.proxy(function () {
                this.element.slideToggle('slow');
            }, this));
            this.closeButton.on('click', $.proxy(this.hide, this));
        },
        hide: function(){
            $(this.element).slideUp('slow', $.proxy(function () {
                clearTimeout(this.timer);
            }, this));
        },
        _onMouseleave: function() {
            clearTimeout(this.timer);
            this.timer = setTimeout($.proxy(function () {
                this.closeButton.trigger('click');
            }, this), this.options.intervalDuration);
        }
    });
})(jQuery);