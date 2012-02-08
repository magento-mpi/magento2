/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function ($) {
    /**
     * Class for managing skin selector control
     */
    DesignEditorSkinSelector = function (config) {
        this._init(config);
        this._addListener();
        return this;
    }

    DesignEditorSkinSelector.prototype._init = function (config) {
        this._skinControlSelector = '#' + config.selectId;
        this._backParams = config.backParams;
        this.changeSkinUrl = config.changeSkinUrl;
        return this;
    }

    DesignEditorSkinSelector.prototype._addListener = function () {
        var thisObj = this;
        $(this._skinControlSelector).change(
            function () {thisObj.changeSkin()}
        );
        return this;
    }

    DesignEditorSkinSelector.prototype.changeSkin = function () {
        var separator = /\?/.test(this.changeSkinUrl) ? '&' : '?';

        var params = {skin: $(this._skinControlSelector).val()};
        for (var i in this._backParams) {
            params[i] = this._backParams[i];
        }

        var url = this.changeSkinUrl + separator + $.param(params);

        window.location.href = url;
        return this;
    }
})(jQuery);
