/**
 * {license_notice}
 *
 * @category    Mage
 * @package     js
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true*/
(function($) {
    "use strict";
    $.widget('mage.centinelAuthenticate', {
        options : {
            relatedBlockSelectors: [],
            frameUrl: '',
            blockSelector: '#centinel-authenticate-block',
            iframeSelector: '#centinel-authenticate-iframe'
        },

        _create : function() {
            this._isAuthenticationStarted = false;
            if (this._isCentinelBlocksLoaded()) {
                $(this.options.blockSelector).hide();
            }
        },

        cancel : function() {
            if (this._isAuthenticationStarted) {
                if (this._isRelatedBlocksLoaded()) {
                    this._showRelatedBlocks();
                }
                if (this._isCentinelBlocksLoaded()) {
                    $(this.options.blockSelector).hide();
                    $(this.options.iframeSelector).prop('src', '');
                }
                this._isAuthenticationStarted = false;
            }
        },

        isAuthenticationStarted : function() {
            return this._isAuthenticationStarted;
        },

        start : function() {
            if (this._isRelatedBlocksLoaded() && this._isCentinelBlocksLoaded()) {
                this._hideRelatedBlocks();
                $(this.options.iframeSelector).prop('src', this.options.frameUrl);
                $(this.options.blockSelector).show();
                this._isAuthenticationStarted = true;
            }
        },

        success : function() {
            if (this._isRelatedBlocksLoaded() && this._isCentinelBlocksLoaded()) {
                this._showRelatedBlocks();
                $(this.options.blockSelector).hide();
                this._isAuthenticationStarted = false;
            }
        },

        _hideRelatedBlocks : function() {
            for (var i = 0; i < this.options.relatedBlockSelectors.size(); i++) {
                $(this.options.relatedBlockSelectors[i]).hide();
            }
        },

        _isCentinelBlocksLoaded : function() {
            return $(this.options.blockSelector).length && $(this.options.iframeSelector).length;
        },

        _isRelatedBlocksLoaded : function() {
            var isLoaded = true;

            for (var i = 0; i < this.options.relatedBlockSelectors.size() && isLoaded; i++) {
                isLoaded = $(this.options.relatedBlockSelectors[i]).length > 0;
            }

            return isLoaded;
        },

        _showRelatedBlocks : function() {
            for (var i = 0; i < this.options.relatedBlockSelectors.size(); i++) {
                $(this.options.relatedBlockSelectors[i]).show();
            }
        }
    });
})(jQuery);
