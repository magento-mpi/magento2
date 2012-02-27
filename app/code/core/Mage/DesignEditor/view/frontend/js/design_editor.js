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
        this._placeholder = null;
        this._highlightingSelector = null;
        this._cbHighlightingClick = null;
        this._highlightingOn = true;

        this._templateWrapper = '<div class="vde_block_wrapper" />';
        this._templateBlockTitle = '<div class="vde_block_title">%BLOCK_NAME%</div>';
        this._templatePlaceholder = '<div class="vde_placeholder"></div>';

        this._wrapBlocks()
            ._enableDragging()
            ._triggerStartedEvent();
        return this;
    }

    DesignEditor.prototype._wrapBlocks = function () {
        var thisObj = this;
        $('.vde_marker[marker_type=start]')
            .filter(function (index) {
                return $(this).parent().css('display') == 'block';
            })
            .each(function (index) {
                var marker = $(this);
                var titleHtml = thisObj._templateBlockTitle.replace('%BLOCK_NAME%', marker.attr('block_name'));
                $(titleHtml).insertAfter(marker);
                marker.nextUntil('.vde_marker[marker_type=end]').wrapAll(thisObj._templateWrapper);
            });
        $('.vde_marker').remove();
        return this;
    }

    DesignEditor.prototype._enableDragging = function () {
        var thisObj = this;
        $('.vde_block_wrapper').draggable({
            helper: 'clone',
            revert: true,
            start: function (event, ui) {thisObj._onDragStarted(event, ui)},
            stop: function (event, ui) {thisObj._onDragStopped(event, ui)}
        });
        return this;
    }

    DesignEditor.prototype._triggerStartedEvent = function () {
        $(document).trigger('started.vde', this);
        return this;
    }

    DesignEditor.prototype._onDragStarted = function (event, ui) {
        if (this._dragged) {
            return this;
        }
        var dragged = $(event.target);
        this._hideDragged(dragged)
            ._resizeHelperSameAsDragged(ui.helper, dragged)
            ._putPlaceholder();
    }

    DesignEditor.prototype._onDragStopped = function (event, ui) {
        if (!this._dragged) {
            return this;
        }
        this._removePlaceholder()
            ._showDragged();
    }

    DesignEditor.prototype._hideDragged = function (dragged) {
        this._showDragged(); // Maybe some other dragged element was hidden before, just retore it
        this._dragged = dragged;
        this._dragged.css('visibility', 'hidden');
        return this;
    }

    DesignEditor.prototype._showDragged = function () {
        if (!this._dragged) {
            return this;
        }
        this._dragged.css('visibility', 'visible');
        this._dragged = null;
        return this;
    }

    DesignEditor.prototype._resizeHelperSameAsDragged = function (helper, dragged) {
        helper.height(dragged.height())
            .width(dragged.width());
        return this;
    }

    DesignEditor.prototype._putPlaceholder = function () {
        if (!this._placeholder) {
            this._placeholder = $(this._templatePlaceholder);
        }
        this._placeholder.css('height', this._dragged.outerHeight() + 'px')
            .css('width', this._dragged.outerWidth() + 'px');
        this._placeholder.insertBefore(this._dragged);
        return this;
    }

    DesignEditor.prototype._removePlaceholder = function () {
        if (!this._placeholder) {
            return this;
        }
        this._placeholder.remove();
        return this;
    }

    DesignEditor.prototype.captureHighlighting = function (highlightingId) {
        this.uncaptureHighlighting()
            ._initHighlightingCallback();

        this._highlightingSelector = '#' + highlightingId;
        $(this._highlightingSelector).on('click', null, this._cbHighlightingClick);

        this._syncHighlightingControl();
        return this;
    }

    DesignEditor.prototype.uncaptureHighlighting = function () {
        if (this._highlightingSelector) {
            $(this._highlightingSelector).off('click', null, this._cbHighlightingClick);
            this._highlightingSelector = null;
        }
        return this;
    }

    DesignEditor.prototype._initHighlightingCallback = function () {
        if (!this._cbHighlightingClick) {
            var thisObj = this;
            this._cbHighlightingClick = function (event) {
                thisObj._onHighlightingClick(event);
            }
        }
        return this;
    }

    DesignEditor.prototype._syncHighlightingControl = function () {
        var control = $(this._highlightingSelector);
        if (control.prop('checked') != this._highlightingOn) {
            control.prop('checked', this._highlightingOn);
        }
        return this;
    }

    DesignEditor.prototype._onHighlightingClick = function (event) {
        this.highlight($(this._highlightingSelector).prop('checked'));
        return this;
    }

    DesignEditor.prototype.highlight = function (isOn) {
        if (!this._highlightingSelector) {
            return this;
        }
        if (this._highlightingOn != isOn) {
            if (isOn) {
                this._turnHighlightingOn();
            } else {
                this._turnHighlightingOff();
            }
        }
        this._syncHighlightingControl();
        return this;
    }

    DesignEditor.prototype._turnHighlightingOn = function () {
        $('.vde_block_wrapper').each(function () {
            var elem = $(this);
            var children = elem.prop('vdeChildren');
            elem.show().append(children).removeProp('vdeChildren');
            elem.children('.vde_block_title').slideDown('fast');
        });

        this._highlightingOn = true;
        return this;
    }

    DesignEditor.prototype._turnHighlightingOff = function () {
        $('.vde_block_wrapper').each(function () {
            var elem = $(this);
            elem.children('.vde_block_title').slideUp('fast', function () {
                var children = elem.children(':not(.vde_block_title)');
                elem.after(children).hide().prop('vdeChildren', children);
            });
        });

        this._highlightingOn = false;
        return this;
    }
})(jQuery);
