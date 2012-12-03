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
        options: {
            url: '',
            locked: false
        },
        loadData: function() {
            var self = this;
            if (this._isLocked()) {
                return
            }
            this._setLocked(true)

            $.ajax({
                url: self._getUrl(),
                type: 'GET',
                dataType: 'JSON',
                success: function(data) {
                    if (data.content) {
                        self.element.find('ul').append(data.content);
                        self._setLocked(false);
                    }
                },
                error: function() {
                    self._setUrl('');
                    throw Error($.mage.__('Some problem with theme loading'));
                }
            });
        },
        _create: function() {
            this._bind();
        },
        _isLocked: function() {
            return this.options.locked;
        },
        _setLocked: function(status) {
            this.options.locked = status;
        },
        _setUrl: function(url) {
            this.options.url = url;
        },
        _getUrl: function(url) {
            return this.options.url;
        },
        _bind: function() {
            var self = this;
            $(document).ready(function() {
                self.loadData();
            });
            this.element.scroll(
                function(event) {
                    if (self._isScrolledBottom() && self._getUrl()) {
                        self.loadData();
                    }
                }
            );
        },
        _isScrolledBottom: function() {
            return (this.element[0].scrollHeight - this.element.scrollTop()) < this.element.outerHeight();
        }
    });

})(jQuery);
