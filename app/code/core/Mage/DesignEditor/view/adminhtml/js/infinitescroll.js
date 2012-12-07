/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

( function ( $ ) {

    $.widget('vde.infinite_scroll', {
        _locked: false,
        _container: '.theme-container',
        _defaultElementSize: 400,
        _elementsInRow: 2,
        _pageSize: 4,
        options: {
            url: ''
        },

        /**
         * Load data
         * @public
         */
        loadData: function() {
            if (this._isLocked()) {
                return
            }
            this._setLocked(true)

            $.ajax({
                url: this.options.url,
                type: 'GET',
                dataType: 'JSON',
                data: { 'page_size': this._pageSize },
                success: $.proxy(function(data) {
                    if (data.content) {
                        this.element.find(this._container).append(data.content);
                        this._setLocked(false);
                    }

                    var eventData = {};
                    this.element.trigger('loaded', eventData);
                }, this),
                error: $.proxy(function() {
                    this.options.url = '';
                    throw Error($.mage.__('Some problem with theme loading'));
                }, this)
            });
        },

        /**
         * Infinite scroll creation
         * @protected
         */
        _create: function() {
            if (this.element.find(this._container).children().length == 0) {
                this._pageSize = this._calculatePagesSize();
            }
            this._bind();
        },

        /**
         * Calculate default pages count
         *
         * @return {number}
         * @protected
         */
        _calculatePagesSize: function() {
            elementsCount = Math.ceil($(window).height() / this._defaultElementSize) * this._elementsInRow;
            return (elementsCount % 2) ? elementsCount++ : elementsCount;
        },

        /**
         * Get is locked
         * @return {boolean}
         * @protected
         */
        _isLocked: function() {
            return this._locked;
        },

        /**
         * Set is locked
         * @param {boolean} status locked status
         * @protected
         */
        _setLocked: function(status) {
            this._locked = status;
        },

        /**
         * Bind handlers
         * @protected
         */
        _bind: function() {
            $(document).ready(
                $.proxy(this.loadData, this)
            );

            $(window).resize(
                $.proxy(function(event) {
                    if (this._isScrolledBottom() && this.options.url) {
                        this.loadData();
                    }
                }, this)
            );

            $(window).scroll(
                $.proxy(function(event) {
                    if (this._isScrolledBottom() && this.options.url) {
                        this.loadData();
                    }
                }, this)
            );
        },

        /**
         * Check is scrolled bottom
         * @return {boolean}
         * @protected
         */
        _isScrolledBottom: function() {
            console.log($(window).scrollTop() + $(window).height());
            console.log($(document).height() - this._defaultElementSize);
            return ($(window).scrollTop() + $(window).height() >= $(document).height() - this._defaultElementSize)
        }
    });

})(jQuery);
