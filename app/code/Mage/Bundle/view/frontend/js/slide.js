/**
 * {license_notice}
 *
 * @category    frontend bundle product slide
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true expr:true*/
(function($) {
    $.widget('mage.slide', {
        options: {
            slideSpeed: 1500,
            slideSelector: '#bundle-slide',
            slideBackSelector: '.bundle-slide-back',
            bundleProductSelector: '#bundleProduct',
            bundleOptionsContainer: '#options-container',
            productViewContainer: '#productView'

        },

        _create: function() {
            $(this.options.slideSelector).on('click', $.proxy(this._slide, this));
            $(this.options.slideBackSelector).on('click', $.proxy(this._slideBack, this));
            this.options.autostart && this._slide();
        },

        /**
         * slide bundleOptionsContainer over to the main view area
         * @private
         */
        _slide: function() {
            $(this.options.bundleProductSelector).css('top', '0px');
            $(this.options.bundleOptionsContainer).show();
            this.element.css('height',$(this.options.productViewContainer).height() + 'px');
            $(this.options.bundleProductSelector).css('left', '0px').animate(
                {'left': '-' + this.element.width() + 'px'},
                this.options.slideSpeed,
                $.proxy(function() {
                    this.element.css('height','auto');
                    $(this.options.productViewContainer).hide();
                }, this)
            );
        },

        /**
         * slideback productViewContainer to main view area
         * @private
         */
        _slideBack: function() {
            $(this.options.bundleProductSelector).css('top', '0px');
            $(this.options.productViewContainer).show();
            this.element.css('height', $(this.options.bundleOptionsContainer).height() + 'px');
            $(this.options.bundleProductSelector).animate(
                {'left': '0px'},
                this.options.slideSpeed,
                $.proxy(function() {
                    $(this.options.bundleOptionsContainer).hide();
                    this.element.css('height','auto');
                }, this)
            );
        }
    });
}(jQuery));