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

    /**
     * Class for design editor
     */
    DesignEditor = function () {
        this._init();
    }

    DesignEditor.prototype._init = function () {
        this._dragged = null;
        this._wrapBlocks();
        this._enableDragging();
        return this;
    }

    DesignEditor.prototype._wrapBlocks = function () {
        $('.vde_marker[marker_type=start]')
            .filter(function (index) {
                return $(this).parent().css('display') == 'block';
            })
            .each(function (index) {
                var marker = $(this);
                $('<div class="vde_block_title">' + marker.attr('block_name') + '</div>').insertAfter(marker);
                marker.nextUntil('.vde_marker').wrapAll('<div class="vde_block_wrapper" />');
            });
        $('.vde_marker').remove();
    }

    DesignEditor.prototype._enableDragging = function () {
        return; // FIXME
        var thisObj = this;
        $('div').not('#vde_toolbar').not('#vde_toolbar div').draggable({
            helper: 'clone',
            revert: true,
            start: function (event, ui) {thisObj._onDragStarted(event, ui)},
            stop: function (event, ui) {thisObj._onDragStopped(event, ui)}
        });
    }

    DesignEditor.prototype._onDragStarted = function (event, ui) {
        this._dragged = $(event.target);
        this._dragged.css('visibility', 'hidden');
        return this;
    }

    DesignEditor.prototype._onDragStopped = function (event, ui) {
        this._dragged.show().css('visibility', 'visible');
        this._dragged = null;
        return this;
    }
})(jQuery);
