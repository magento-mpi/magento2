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
                url: this._getUrl(),
                type: 'GET',
                dataType: 'JSON',
                success: $.proxy(function(data) {
                    if (data.content) {
                        this.element.find('ul').append(data.content);
                        this._setLocked(false);
                    }
                }, this),
                error: $.proxy(function() {
                    this._setUrl('');
                    throw Error($.mage.__('Some problem with theme loading'));
                }, this)
            });
        },

        /**
         * Infinite scroll creation
         * @protected
         */
        _create: function() {
            this._bind();
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
         * Set url
         * @param {string} url next page url
         * @protected
         */
        _setUrl: function(url) {
            this.options.url = url;
        },

        /**
         * Get url
         * @return {string}
         * @protected
         */
        _getUrl: function() {
            return this.options.url;
        },

        /**
         * Bind handlers
         * @protected
         */
        _bind: function() {
            $(document).ready(
                $.proxy(this.loadData, this)
            );

            this.element.scroll(
                $.proxy(function(event) {
                    if (this._isScrolledBottom() && this._getUrl()) {
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
            return (this.element[0].scrollHeight - this.element.scrollTop()) < this.element.outerHeight();
        }
    });

})(jQuery);
