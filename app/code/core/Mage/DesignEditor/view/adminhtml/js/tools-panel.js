/**
 * {license_notice}
 *
 * @category    design
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true*/
(function($) {
    'use strict';
    $.widget('vde.toolsPanel', {
        options: {
            openedPanelClass: 'opened',
            activeTabClass: 'active',
            panelDefaultHeight: 500,
            showHidePanelAnimationSpeed: 300,
            resizableArea: '.vde-tools-content',
            resizableAreaInner: '.vde-tab-content.active .vde-tab-content-inner',
            panelHeader: '.vde-tab-content-header',
            panelTab: 'a[data-toggle="tab"]',
            resizeHandlerControl: '.ui-resizable-handle',
            resizeHandlerControlContainer: '.vde-tools-handler-container',
            scrollExistClass: 'hasScroll',
            mainTabs: '.vde-tools-footer .vde-tab-controls > .item',
            btnClose: '.vde-tools-header .action-close',
            btnCloseMsg: '.vde-message .action-close'
        },

        _create: function() {
            this.panel = this.element;

            this.resizableArea = $(this.options.resizableArea);
            this.resizableAreaInner = $(this.options.resizableAreaInner);
            this.panelTab = $(this.options.panelTab);
            this.resizeHandlerControlContainer = $(this.options.resizeHandlerControlContainer);
            this.panelHeaderHeight = $(this.options.panelHeader).height();
            this.btnClose = $(this.options.btnClose, this.panel);
            this.mainTabs = $(this.options.mainTabs);
            this.btnCloseMsg = $(this.options.btnCloseMsg, this.panel);

            this._events();
        },

        _init: function() {
            $(this.options.resizeHandlerControl).prependTo(this.resizeHandlerControlContainer);
            this._recalcDataHeight(this._getResizableAreaHeight());
        },

        _events: function() {
            var self = this;

            this.resizableArea
                .resizable({
                    handles: 'n',
                    minHeight: 100,
                    maxHeight: 700,
                    resize: function(event, ui) {
                        self._recalcDataHeight(ui.size.height);
                    }
                }).bind('resize.vdeToolsResize', function () {
                    self._recalcDataHeight(self._getResizableAreaHeight());
                    $(this).css('top', 'auto');
                });

            this.panelTab
                .on('shown', function () {
                    if (!self.panel.hasClass(self.options.openedPanelClass)) {
                        self._show();
                    } else {
                        self._recalcDataHeight(self.options.panelDefaultHeight);
                    }
                    self.resizableArea.trigger('resize.vdeToolsResize');
                });

            this.btnClose
                .live('click.hideVDEToolsPanel', $.proxy(this._hide, this));

            this.btnCloseMsg.live('click.hideVDEMessage', $.proxy(function(e) {
                $(e.target).parents('.vde-message')[0].remove();
            }, this));
        },

        _toggleClassIfScrollBarExist: function(elem) {
            elem.toggleClass(this.options.scrollExistClass, elem.get(0).scrollHeight > elem.height());
        },

        _getActiveResizableAreaInner: function() {
            return $(this.options.resizableAreaInner);
        },

        _getResizableAreaHeight: function() {
            return this.resizableArea.height();
        },

        _recalcDataHeight: function(height) {
            var elem = this._getActiveResizableAreaInner();

            elem.height(height - this.panelHeaderHeight);
            this._toggleClassIfScrollBarExist(elem);
        },

        _show: function() {
            var self = this;

            this.panel.addClass(this.options.openedPanelClass);

            this.resizableArea.animate({
                height: self.options.panelDefaultHeight - self.panelHeaderHeight
            }, self.options.showHidePanelAnimationSpeed, function() {
                self.resizableArea.trigger('resize.vdeToolsResize');
            });
        },

        _hide: function() {
            var self = this;

            this.resizableArea.animate({
                height: 0
            }, self.options.showHidePanelAnimationSpeed, function() {
                self.panel
                    .removeClass(self.options.openedPanelClass);

                self.mainTabs
                    .removeClass(self.options.activeTabClass);
            });
        }
    });
})(window.jQuery);
