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
        _loader: '.theme-loader',
        _container: '.theme-container',
        _defaultElementSize: 400,
        _elementsInRow: 2,
        _pageSize: 4,
        options: {
            url: '',
            clear: false,
            loadDataOnCreate: true
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
                context: $(this),
                success: $.proxy(function(data) {
                    if (data.content) {
                        if (this.options.url === '') {
                            this._setLocked(false);
                            return;
                        }
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

            if (this.options.clear) {
                this.clearOldData();
            }
            this._bind();
        },

        /**
         * Clear old data
         * @protected
         */
        clearOldData: function() {
            this.options.clear = false;
            $(this._container).empty();
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
            (status) ? $(this._loader).show() : $(this._loader).hide();
            this._locked = status;
        },

        /**
         * Bind handlers
         * @protected
         */
        _bind: function() {
            if (this.options.loadDataOnCreate) {
                $(document).ready(
                    $.proxy(this.loadData, this)
                );
            }

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
            return ($(window).scrollTop() + $(window).height() >= $(document).height() - this._defaultElementSize)
        }
    });

})(jQuery);
