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

    $.widget("vde.translateInlineToggle", {
        _create: function() {
            this.element.on('click', $.proxy(this._onClick, this));
        },

        /**
        * This method displays the translate menu.
        */
        _onClick: function () {
            $('[translate-menu]').toggleClass('hidden');
        }
    });

    $.widget("vde.translateInlineToggleMode", {
        options: {
            frameUrl: null,
            refreshVdeCanvas: false,
            disableInlineTranslation: false,
            frameBody: null
        },

        _create: function() {
            this.element.on('click', $.proxy(this._onClick, this));
        },

        /**
        * This method will only enable editing for the translation mode specified.
        *
        * If this is the first time a mode is selected, the contents of the iframe will be wrapped with the appropriate
        * attributes where applicable (translate-mode, either 'text', 'script' or 'alt').
        *
        */
        _onClick: function () {
            // Hide menu.
            $('[translate-menu]').toggleClass('hidden');

            // Change menu to reflect what was selected, so will display correctly when displayed again.
            this._updateMenu(this.element.attr('data-translate-selected'));

            // Refresh iframe with new url
            this._refresh(this.element.attr('data-translate-selected'));
        },

        /**
        * This method refreshes the icons, background and toolbar button based on the currently selected menu option.
        *
        * @param mode
        * @private
        */
        _refresh: function (mode) {
            $('[spinner]').toggleClass('hidden');

            var url = this.options.frameUrl;
            if (!this.options.disableInlineTranslation)
                url = url + "translation_mode/" + mode;

            // If this is the first time selecting a mode, refresh the iframe to wrap all the applicable content.
            // Or, if disabling inline translation, refresh minus the translation mode on the url.
            if (this.options.refreshVdeCanvas || this.options.disableInlineTranslation)
                $('[data-frame="editor"]').prop('src', url);
            else {
                this.options.frameBody.translateInlineDialogVde('toggleStyle', mode);
                this.options.textTranslations.translateInlineVde('toggleIcon', mode);
                this.options.imageTranslations.translateInlineImageVde('toggleIcon', mode);
                this.options.scriptTranslations.translateInlineScriptVde('toggleIcon', mode);
            }

            /**
            * Since the url is being modified to support inline translation, the window is not reloaded since it
            * is using the url from the cache to display.
            */

            $('[spinner]').toggleClass('hidden');
        },

        /**
        * This method updates the menu's current status.
        *
        * @param mode
        * @private
        */
        _updateMenu: function (mode) {
            function _toggleSelected (translateOption, mode, enableOnToolbar, backgroundRemove, backgroundAdd, imageRemove, imageAdd) {
                translateOption.removeClass(backgroundRemove).addClass(backgroundAdd);
                translateOption.children('[data-translate-img]').removeClass(imageRemove).addClass(imageAdd);

                // Update toolbar button.
                var textEditClassOn = 'text-edit-' + mode + '-on';
                if (enableOnToolbar)
                    $('[vde-translate-edit]').addClass(textEditClassOn);
                else
                    $('[vde-translate-edit]').removeClass(textEditClassOn);
            }

            var disableInlineTranslation = false;

            var TEXT_MENU_BACKGROUND_ON = 'text-menu-background-on';
            var TEXT_MENU_BACKGROUND_OFF = 'text-menu-background-off';

            var textMenuClass = 'text-menu-' + mode;

            $('[data-translate-selected]').each(function() {
                if ($(this).attr('data-translate-selected') === mode) {
                    // Check to see if turning off (selecting the already highlighted option).
                    if ($(this).hasClass(TEXT_MENU_BACKGROUND_ON)) {
                        // Disable option.
                        _toggleSelected($(this), mode, false, TEXT_MENU_BACKGROUND_ON, TEXT_MENU_BACKGROUND_OFF, textMenuClass + '-on', textMenuClass + '-off');

                        // Refresh iframe minus the translation mode on the url.
                        disableInlineTranslation = true;
                    }
                    else
                        // Enable selected option
                        _toggleSelected($(this), mode, true, TEXT_MENU_BACKGROUND_OFF, TEXT_MENU_BACKGROUND_ON, textMenuClass + '-off', textMenuClass + '-on');
                }
                else {
                    // Disable option.
                    var translateOptionMode = $(this).attr('data-translate-selected');
                    var translateOptionModeClass = 'text-menu-' + translateOptionMode;

                    _toggleSelected($(this), translateOptionMode, false, TEXT_MENU_BACKGROUND_ON, TEXT_MENU_BACKGROUND_OFF, translateOptionModeClass + '-on', translateOptionModeClass + '-off');
                }
            });

            this.options.disableInlineTranslation = disableInlineTranslation;
        }
    });

})(window.jQuery);