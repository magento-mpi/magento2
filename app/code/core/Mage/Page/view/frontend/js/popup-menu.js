/*jshint browser:true jquery:true*/
(function($) {
    $.widget('mage.popUpMenu', {
        options: {
            duration: 2000 // Timeout duration
        },

        /**
         * Add click event to the switcher. Add blur, mouseenter/mouseleave events to the
         * containing element.
         * @private
         */
        _create: function() {
            this.switcher = this.element.find('span.switcher')
                .on('click', $.proxy(this._toggleMenu, this));
            this.element
                .on('blur', $.proxy(this._hide, this))
                .on('mouseleave', $.proxy(this._onMouseleave, this))
                .on('mouseenter', $.proxy(this._onMouseenter, this));
            $(this.options.menu).find('a')
                .on('click', $.proxy(this._hide, this));
        },

        /**
         * Hide the popup menu using a fade effect.
         * @private
         */
        _hide: function(){
            $(this.options.menu).fadeOut('slow', $.proxy(this._stopTimer, this));
            this.switcher.removeClass('list-opened');
        },

        /**
         * Show the popup menu using a fade effect and put focus on the containing element
         * for the blur event.
         * @private
         */
        _show: function() {
            $(this.options.menu).removeClass('faded').fadeIn('slow');
            this.switcher.addClass('list-opened');
            this.element.focus();
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
            return this.switcher.hasClass('list-opened');
        },

        /**
         * Mouseleave event on the popup menu. Add faded class and set appropriate timeout.
         * @private
         */
        _onMouseleave: function() {
            if (this._isOpened()) {
                $(this.options.menu).addClass('faded');
                this._stopTimer();
                this.timer = setTimeout($.proxy(this._hide, this), this.options.duration);
            }
        },

        /**
         * Mouseenter event on the popup menu. Reset the timer and remove the faded class.
         * @private
         */
        _onMouseenter: function() {
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
