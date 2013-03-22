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

    /**
     * Toggle editing.
     *
     * @param element
     * @param frameUrl
     * @param refreshVdeCanvas
     * @param frameBody
     * @param textTranslations
     * @param imageTranslations
     * @param scriptTranslations
     * @private
     */
    function _toggle(element, frameUrl, refreshVdeCanvas, frameBody, textTranslations, imageTranslations, scriptTranslations) {
        // Hide menu.
        if (!element.closest('[data-translate-menu]').hasClass('hidden'))
            element.closest('[data-translate-menu]').toggleClass('hidden');

        // Change menu to reflect what was selected, so will display correctly when displayed again.
        var disableInlineTranslation = _updateMenu(element.attr('data-translate-selected'));

        // Refresh iframe with the new url.
        _refresh(element.attr('data-translate-selected'), disableInlineTranslation, frameUrl, refreshVdeCanvas, frameBody, textTranslations, imageTranslations, scriptTranslations);
    }

    /**
     * Refresh the iframe.
     *
     * @param mode
     * @param disableInlineTranslation
     * @param url
     * @param refreshVdeCanvas
     * @param frameBody
     * @param textTranslations
     * @param imageTranslations
     * @param scriptTranslations
     * @private
     */
    function _refresh(mode, disableInlineTranslation, url, refreshVdeCanvas, frameBody, textTranslations, imageTranslations, scriptTranslations) {
        // If this is the first time selecting a mode, refresh the iframe to wrap all the applicable content.
        // Or, if disabling inline translation, refresh without the mode on the url.
        if (refreshVdeCanvas || disableInlineTranslation) {
            if (refreshVdeCanvas)
                url = url + "translation_mode/" + mode;

            $('[data-frame="editor"]').prop('src', url);

            /**
             * Since the url is being modified to support inline translation, the window is not reloaded since it
             * is using the cached url to display.
             */
        }
        else {
            frameBody.translateInlineDialogVde('toggleStyle', mode);
            textTranslations.translateInlineVde('toggleIcon', mode);
            imageTranslations.translateInlineImageVde('toggleIcon', mode);
            scriptTranslations.translateInlineScriptVde('toggleIcon', mode);
        }
    }

    /**
     * Update the menu and toolbar button.
     *
     * @param mode
     * @private
     */
    function _updateMenu(mode) {
        function _toggleSelected (translateOption, backgroundRemove, backgroundAdd, imageRemove, imageAdd) {
            translateOption.removeClass(backgroundRemove).addClass(backgroundAdd);
            translateOption.children('[data-translate-img]').removeClass(imageRemove).addClass(imageAdd);
        }

        var disableInlineTranslation = false;

        var TEXT_MENU_BACKGROUND_ON = 'text-menu-background-on';
        var TEXT_MENU_BACKGROUND_OFF = 'text-menu-background-off';

        var textMenuClass = 'text-menu-' + mode;
        var textEditClass = 'text-edit-' + mode;

        $('[data-translate-edit]').attr('data-translate-edit', mode);

        $('[data-translate-selected]').each(function() {
            if ($(this).attr('data-translate-selected') === mode) {
                // Check to see if turning off (selecting the already highlighted option).
                if ($(this).hasClass(TEXT_MENU_BACKGROUND_ON)) {
                    // Update toolbar button.
                    $('[data-translate-edit]').removeClass(textEditClass + '-on');
                    $('[data-translate-edit]').addClass(textEditClass + '-off');

                    // Disable option.
                    _toggleSelected($(this), TEXT_MENU_BACKGROUND_ON, TEXT_MENU_BACKGROUND_OFF, textMenuClass + '-on', textMenuClass + '-off');

                    // Refresh iframe without the mode on the url.
                    disableInlineTranslation = true;
                }
                else {
                    // Update toolbar button.
                    $('[data-translate-edit]').removeClass(textEditClass + '-off');
                    $('[data-translate-edit]').addClass(textEditClass + '-on');

                    // Enable selected option
                    _toggleSelected($(this), TEXT_MENU_BACKGROUND_OFF, TEXT_MENU_BACKGROUND_ON, textMenuClass + '-off', textMenuClass + '-on');
                }

                // Update tooltip text.
                $('[data-tip-text]').text("Toggle " + $(this).children('[data-translate-label]').html());
            }
            else {
                var translateOptionMode = $(this).attr('data-translate-selected');
                var translateOptionModeClass = 'text-menu-' + translateOptionMode;
                var translateEditModeClass = 'text-edit-' + translateOptionMode;

                // Update toolbar button.
                $('[data-translate-edit]').removeClass(translateEditModeClass + '-on');
                $('[data-translate-edit]').removeClass(translateEditModeClass + '-off');

                // Disable option.
                _toggleSelected($(this), TEXT_MENU_BACKGROUND_ON, TEXT_MENU_BACKGROUND_OFF, translateOptionModeClass + '-on', translateOptionModeClass + '-off');
            }
        });

        return disableInlineTranslation;
    }

    $.widget("vde.translateInlineToggle", {
        _create: function() {
            this.element.on('mouseover', $.proxy(this._onMouseOver, this))
                        .on('mouseout', $.proxy(this._onMouseOut, this))
                        .on('mousedown', $.proxy(this._onMouseDown, this))
                        .on('mouseup', $.proxy(this._onMouseUp, this));
        },

        /**
         * If the menu is not being shown, show the tooltip.
         *
         * @private
         */
        _onMouseOver: function () {
            if ($('[data-translate-menu]').hasClass('hidden'))
                $('[data-tip="translate"]').removeClass('hidden');
        },

        /**
         * Hide the tooltip.
         *
         * @private
         */
        _onMouseOut: function () {
            $('[data-tip="translate"]').addClass('hidden');
        },

        /**
         * If the mouse button has been held down for more than 1 second, the menu will be displayed.
         *
         * @private
         */
        _onMouseDown: function () {
            $('[data-tip="translate"]').addClass('hidden');

            clearTimeout(this.downTimer);
            this.downTimer = setTimeout(function() {
                $('[data-translate-menu]').toggleClass('hidden');
            }, 1000);
        },

        /**
         * If the menu is not displaying (didn't hold down button long enough),
         * toggle text mode, else hide the tooltip.
         *
         * @private
         */
        _onMouseUp: function () {
            if ($('[data-translate-menu]').hasClass('hidden')) {
                var frameUrl = this.options.frameUrl;
                var refreshVdeCanvas = this.options.refreshVdeCanvas;
                var frameBody = this.options.frameBody;
                var textTranslations = this.options.textTranslations;
                var imageTranslations = this.options.imageTranslations;
                var scriptTranslations = this.options.scriptTranslations;

                $('[data-translate-selected]').each(function() {
                    if ($(this).attr('data-translate-selected') === $('[data-translate-edit]').attr('data-translate-edit'))
                        _toggle($(this), frameUrl, refreshVdeCanvas, frameBody, textTranslations, imageTranslations, scriptTranslations);
                });
            }
            else
                $('[data-tip="translate"]').addClass('hidden');

            clearTimeout(this.downTimer);
        }
    });

    $.widget("vde.translateInlineToggleMode", {
        _create: function() {
            this.element.on('click', $.proxy(this._onClick, this));
        },

        /**
        * Toggle editing.
        */
        _onClick: function () {
            var frameUrl = this.options.frameUrl;
            var refreshVdeCanvas = this.options.refreshVdeCanvas;
            var frameBody = this.options.frameBody;
            var textTranslations = this.options.textTranslations;
            var imageTranslations = this.options.imageTranslations;
            var scriptTranslations = this.options.scriptTranslations;

            _toggle(this.element, frameUrl, refreshVdeCanvas, frameBody, textTranslations, imageTranslations, scriptTranslations);
        }
    });

})(window.jQuery);