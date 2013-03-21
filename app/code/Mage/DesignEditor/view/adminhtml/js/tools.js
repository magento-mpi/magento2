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
     * This method will only enable editing for the translation mode specified.
     *
     * If this is the first time a mode is selected, the contents of the iframe will be wrapped with the appropriate
     * attributes where applicable (translate-mode, either 'text', 'script' or 'alt').
     *
     */
    function toggle(element, frameUrl, refreshVdeCanvas, frameBody, textTranslations, imageTranslations, scriptTranslations) {
        // Hide menu.
        if (!$('[translate-menu]').hasClass('hidden'))
            $('[translate-menu]').toggleClass('hidden');

        // Change menu to reflect what was selected, so will display correctly when displayed again.
        var disableInlineTranslation = updateMenu(element.attr('data-translate-selected'));

        // Refresh iframe with new url
        refresh(element.attr('data-translate-selected'), disableInlineTranslation, frameUrl, refreshVdeCanvas, frameBody, textTranslations, imageTranslations, scriptTranslations);
    }

    /**
     * This method refreshes the icons, background and toolbar button based on the currently selected menu option.
     *
     * @param mode
     * @param disableInlineTranslation
     * @param url
     * @param refreshVdeCanvas
     * @param frameBody
     * @param textTranslations
     * @param imageTranslations
     * @param scriptTranslations
     */
    function refresh(mode, disableInlineTranslation, url, refreshVdeCanvas, frameBody, textTranslations, imageTranslations, scriptTranslations) {
        $('[spinner]').toggleClass('hidden');

        if (!disableInlineTranslation)
            url = url + "translation_mode/" + mode;

        // If this is the first time selecting a mode, refresh the iframe to wrap all the applicable content.
        // Or, if disabling inline translation, refresh minus the translation mode on the url.
        if (refreshVdeCanvas || disableInlineTranslation)
            $('[data-frame="editor"]').prop('src', url);
        else {
            frameBody.translateInlineDialogVde('toggleStyle', mode);
            textTranslations.translateInlineVde('toggleIcon', mode);
            imageTranslations.translateInlineImageVde('toggleIcon', mode);
            scriptTranslations.translateInlineScriptVde('toggleIcon', mode);
        }

        /**
         * Since the url is being modified to support inline translation, the window is not reloaded since it
         * is using the url from the cache to display.
         */

        $('[spinner]').toggleClass('hidden');
    }

    /**
     * This method updates the menu's current status.
     *
     * @param mode
     * @private
     */
    function updateMenu(mode) {
        function _toggleSelected (translateOption, backgroundRemove, backgroundAdd, imageRemove, imageAdd) {
            translateOption.removeClass(backgroundRemove).addClass(backgroundAdd);
            translateOption.children('[data-translate-img]').removeClass(imageRemove).addClass(imageAdd);
        }

        var disableInlineTranslation = false;

        var TEXT_MENU_BACKGROUND_ON = 'text-menu-background-on';
        var TEXT_MENU_BACKGROUND_OFF = 'text-menu-background-off';

        var textMenuClass = 'text-menu-' + mode;
        var textEditClass = 'text-edit-' + mode;

        $('[vde-translate-edit]').attr('vde-translate-edit', mode);

        $('[data-translate-selected]').each(function() {
            if ($(this).attr('data-translate-selected') === mode) {
                // Check to see if turning off (selecting the already highlighted option).
                if ($(this).hasClass(TEXT_MENU_BACKGROUND_ON)) {
                    // Update toolbar button.
                    $('[vde-translate-edit]').removeClass(textEditClass + '-on');
                    $('[vde-translate-edit]').addClass(textEditClass + '-off');

                    // Disable option.
                    _toggleSelected($(this), TEXT_MENU_BACKGROUND_ON, TEXT_MENU_BACKGROUND_OFF, textMenuClass + '-on', textMenuClass + '-off');

                    // Refresh iframe minus the translation mode on the url.
                    disableInlineTranslation = true;
                }
                else {
                    // Update toolbar button.
                    $('[vde-translate-edit]').removeClass(textEditClass + '-off');
                    $('[vde-translate-edit]').addClass(textEditClass + '-on');

                    // Enable selected option
                    _toggleSelected($(this), TEXT_MENU_BACKGROUND_OFF, TEXT_MENU_BACKGROUND_ON, textMenuClass + '-off', textMenuClass + '-on');
                }
            }
            else {
                var translateOptionMode = $(this).attr('data-translate-selected');
                var translateOptionModeClass = 'text-menu-' + translateOptionMode;
                var translateEditModeClass = 'text-edit-' + translateOptionMode;

                // Update toolbar button.
                $('[vde-translate-edit]').removeClass(translateEditModeClass + '-on');
                $('[vde-translate-edit]').removeClass(translateEditModeClass + '-off');

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
         * This method displays the tooltip dialog.
         *
         * @private
         */
        _onMouseOver: function () {
            /** Only display if menu is not being shown */
            if ($('[translate-menu]').hasClass('hidden'))
                $('[data-tip="translate"]').removeClass('hidden');
        },

        /**
         * This method hides the tooltip dialog.
         *
         * @private
         */
        _onMouseOut: function () {
            $('[data-tip="translate"]').addClass('hidden');
        },

        /**
         * This method will check to see if the mouse button has been held down for more than 1 second.
         * If it has, then the menu should be displayed, else assume a toggle of the current text mode.
         *
         * @private
         */
        _onMouseDown: function () {
            $('[data-tip="translate"]').addClass('hidden');

            clearTimeout(this.downTimer);
            this.downTimer = setTimeout(function() {
                $('[translate-menu]').toggleClass('hidden');
            }, 1000);
        },

        _onMouseUp: function () {
            /**
             * If the menu is not displaying (didn't hold down button long enough), toggle text mode,
             * else just hide the tooltip.
             */
            var frameUrl = this.options.frameUrl;
            var refreshVdeCanvas = this.options.refreshVdeCanvas;
            var frameBody = this.options.frameBody;
            var textTranslations = this.options.textTranslations;
            var imageTranslations = this.options.imageTranslations;
            var scriptTranslations = this.options.scriptTranslations;

            if ($('[translate-menu]').hasClass('hidden')) {
                $('[data-translate-selected]').each(function() {
                    if ($(this).attr('data-translate-selected') === $('[vde-translate-edit]').attr('vde-translate-edit'))
                        toggle($(this), frameUrl, refreshVdeCanvas, frameBody, textTranslations, imageTranslations, scriptTranslations);
                });
            }
            else
                $('[data-tip="translate"]').addClass('hidden');

            clearTimeout(this.downTimer);
        }
    });

    $.widget("vde.translateInlineToggleMode", {
        options: {
            frameUrl: null,
            refreshVdeCanvas: false,
            frameBody: null
        },

        _create: function() {
            this.element.on('click', $.proxy(this.onClick, this));
        },

        /**
        * This method will only enable editing for the translation mode specified.
        *
        * If this is the first time a mode is selected, the contents of the iframe will be wrapped with the appropriate
        * attributes where applicable (translate-mode, either 'text', 'script' or 'alt').
        *
        */
        onClick: function () {
            var frameUrl = this.options.frameUrl;
            var refreshVdeCanvas = this.options.refreshVdeCanvas;
            var frameBody = this.options.frameBody;
            var textTranslations = this.options.textTranslations;
            var imageTranslations = this.options.imageTranslations;
            var scriptTranslations = this.options.scriptTranslations;

            toggle(this.element, frameUrl, refreshVdeCanvas, frameBody, textTranslations, imageTranslations, scriptTranslations);
        }
    });

})(window.jQuery);