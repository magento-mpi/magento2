/**
 * {license_notice}
 *
 * @category    popup-menu
 * @package     js
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function($) {
    $.widget('mage.popUpMenu', {
        options: {
            fadeDuration: 'slow',
            hideOnClick: true,
            openedClass: 'list-opened',
            switcher: 'span.switcher',
            timeoutDuration: 2000
        },

        /**
         * Add click event to the switcher. Add blur, mouseenter/mouseleave events to the
         * containing element.
         * @private
         */
        _create: function() {
            this.switcher = this.element.find(this.options.switcher)
                .on('click', $.proxy(this._toggleMenu, this));
            var eventMap = this.options.hideOnClick ? {'blur': $.proxy(this._hide, this)} : {};
            $.extend(eventMap, {
                'mouseenter': $.proxy(this.options.onMouseEnter, this),
                'mouseleave': $.proxy(this.options.onMouseLeave, this)
            });
            this.element.on(eventMap);
            $(this.options.menu).find('a').on('click', $.proxy(this._hide, this));
        },

        /**
         * Custom method for defining options during instantiation. User-provided options
         * override the options returned by this method which override the default options.
         * @return {Object} Object containing options for mouseenter/mouseleave events.
         * @private
         */
        _getCreateOptions: function() {
            return {onMouseEnter: this._onMouseEnter, onMouseLeave: this._onMouseLeave};
        },

        /**
         * Hide the popup menu using a fade effect.
         * @private
         */
        _hide: function(){
            $(this.options.menu).fadeOut(this.options.fadeDuration, $.proxy(this._stopTimer, this));
            this.switcher.removeClass(this.options.openedClass);
        },

        /**
         * Show the popup menu using a fade effect and put focus on the containing element for
         * the blur event.
         * @private
         */
        _show: function() {
            $(this.options.menu).removeClass('faded').fadeIn(this.options.fadeDuration);
            this.switcher.addClass(this.options.openedClass);
            if (this.options.hideOnClick) {
                this.element.focus();
            }
        },

        /**
         * Stop (clear) the timeout.
         * @private
         */
        _stopTimer: function() {
            clearTimeout(this.timer);
        },

        /**
         * Determines whether the popup menu is open (show) or closed (hide).
         * @return boolean Returns true if open, false otherwise.
         * @private
         */
        _isOpened: function() {
            return this.switcher.hasClass(this.options.openedClass);
        },

        /**
         * Mouseleave event on the popup menu. Add faded class and set appropriate timeout.
         * @private
         */
        _onMouseLeave: function() {
            if (this._isOpened()) {
                $(this.options.menu).addClass('faded');
                this._stopTimer();
                this.timer = setTimeout($.proxy(this._hide, this), this.options.timeoutDuration);
            }
        },

        /**
         * Mouseenter event on the popup menu. Reset the timer and remove the faded class.
         * @private
         */
        _onMouseEnter: function() {
            if (this._isOpened()) {
                this._stopTimer();
                $(this.options.menu).removeClass('faded');
            }
        },

        /**
         * Toggle the state of the popup menu. Open it (show) or close it (hide).
         * @return {*}
         * @private
         */
        _toggleMenu: function() {
            return this[this._isOpened() ? '_hide' : '_show']();
        }
    });
})(jQuery);
