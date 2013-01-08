/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true */
/*global window:true FORM_KEY:true*/
(function($) {
    "use strict";
    $.widget('mage.floatingHeader', {
        options: {
            skipSelector: '.skip-header',
            placeholderAttrs: {
                'class': 'content-header-placeholder'
            },
            fixedClass: 'fixed'
        },

        _create: function() {
            // Do not init widget if there is a skip class applied
            if(this.element.hasClass(this.options.skipSelector)) {
                this.destroy();
            }

            this._setVars();
            this._bind();
        },

        _setVars: function() {
            this._placeholder = this.element.before($('<div/>', this.options.placeholderAttrs)).prev();
            this._offsetTop = this._placeholder.offset().top;
            this._height = this.element.outerHeight(true);
        },

        _bind: function() {
            this._on(window, {
                scroll: this._handlePageScroll,
                resize: this._handlePageScroll
            });
        },

        _handlePageScroll: function() {
            var isActive = ($(window).scrollTop() > this._offsetTop);
            this.element
                [isActive ? 'addClass': 'removeClass'](this.options.fixedClass);
            this._placeholder.height(isActive ? this._height: '');
        },

        _destroy: function() {
            this._placeholder.remove();
            this._off($(window));
        }
    });

    $(function() {
        $('.content-header').floatingHeader();
    });
})(jQuery);
