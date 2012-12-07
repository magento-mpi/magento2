/**
 * {license_notice}
 *
 * @category    mage
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true*/
(function($) {
    $.widget("mage.themeSelector", {
        options: {
            assignEvent:  'assign',
            previewEvent: 'preview',
            loadEvent:    'loaded',
            storeView: {
                windowSelector: '#store-view-window',
                assignSaveButtonRelativeSelector: 'button'
            },
            url: null,
            storesByThemes: {}
        },

        /**
         * Identifier of a theme currently processed
         *
         * It is set in showStoreViews(), used and then cleared in _onAssignSave()
         */
        themeId: null,

        /**
         * Form creation
         * @protected
         */
        _create: function() {
            this._bind();
        },

        /**
         * Bind handlers
         * @protected
         */
        _bind: function() {
            this.element.on(this.options.assignEvent, $.proxy(this._onAssign, this));
            this.element.on(this.options.previewEvent, $.proxy(this._onPreview, this));

            $('body').on(this.options.loadEvent, function() {
                $('*[data-widget-button]').button();
            });

            var window = $(this.options.storeView.windowSelector)
                .find(this.options.storeView.assignSaveButtonRelativeSelector)
                .on('click', $.proxy(this._onAssignSave, this));
        },

        /**
         * Preview action
         * @protected
         */
        _onPreview: function(event, data) {
            document.location = data.preview_url;
        },

        /**
         * Assign event handler
         * @protected
         */
        _onAssign: function(event, data) {
            //TODO This is just a mock
            this.options.isMultipleStoreMode = true;
            if (this.options.isMultipleStoreMode) {
                this.showStoreViews(data.theme_id);
            } else {
                if (!this._confirm('You are about to change this theme for your live store, are you sure want to do this?')) {
                    return;
                }
                this.assignTheme(data.theme_id, null);
            }
        },

        /**
         * "Assign Save" button click handler
         * @protected
         */
        _onAssignSave: function(event, data) {
            var window = $(this.options.storeView.windowSelector);
            window.hide();

            var stores = [];
            var checkedValue = 1;
            $(this.options.storeView.windowSelector).find('form').serializeArray().each(function(object, index) {
                if (object.value == checkedValue) {
                    stores.push(object.name.match('storeviews\\[(\\d)\\]')[1] * 1);
                }
            });

            this.assignSaveTheme(this.themeId, stores);
            this.themeId = null;
        },

        /**
         * Assign event handlers
         * @protected
         */
        _confirm: function(message) {
            return confirm(message);
        },

        /**
         * Show store-view selector window
         * @public
         */
        showStoreViews: function(themeId) {
            var popUp = $(this.options.storeView.windowSelector);
            var storesByThemes = this.options.storesByThemes;
            popUp.find('input[type=checkbox]').each(function(index, element) {
                element = $(element);
                var storeViewId = element.attr('id').replace('storeview_', '') * 1;
                var checked = true;
                if (!storesByThemes[themeId] || storesByThemes[themeId].indexOf(storeViewId) == -1) {
                    checked = false;
                }
                element.attr('checked', checked);
            });
            this.themeId = themeId;
            popUp.show();
        },

        /**
         * Send AJAX request to assign theme to store-views
         * @public
         */
        assignSaveTheme: function(themeId, stores) {
            var message = 'current store';
            if (stores !== null) {
                message = 'stores: "' + stores.join(', ') + '"';
            }
            if (!this.options.url) {
                throw Error('Url to assign themes to store is not defined');
            }
            $.post(this.options.url, stores, function(data) {
                alert('Theme "' + themeId + '" successfully assigned to ' + message);
            });
            this.options.storesByThemes[themeId] = stores;

        }
    });
})(jQuery);
