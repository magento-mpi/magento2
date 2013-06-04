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
    /**
     * Dialog button title
     *
     * @const
     * @type {string}
     */
    var BUTTON_ASSIGN = 'OK';

    /**
     * Dialog button title
     *
     * @const
     * @type {string}
     */
    var BUTTON_EDIT = 'Edit';

    //TODO since we can't convert data to JSON string we use magic numbers DEFAULT_STORE and EMPTY_STORES

    /**
     * Magic number to send via AJAX request to notify that theme should be assigned to default store
     *
     * @const
     * @type {number}
     */
    var DEFAULT_STORE = -1;

    /**
     * Magic number to send via AJAX request to notify that theme should be unassigned from every store
     *
     * @const
     * @type {number}
     */
    var EMPTY_STORES = -2;

    'use strict';
    /**
     * VDE assign theme widget
     */
    $.widget('vde.themeAssign', {
        options: {
            assignEvent:            'assign',
            assignConfirmEvent:     'assign-confirm',
            loadEvent:              'loaded',
            beforeShowStoresEvent:  'show-stores-before',
            dialogSelectorMS:       '#dialog-message-assign',
            dialogSelector:         '#dialog-message-confirm',
            closePopupBtn:          '[class^="action-close"]',
            assignUrl:              null,
            afterAssignRedirectUrl: null,
            storesByThemes:         [],
            hasMultipleStores:      null,
            actionOnAssign:         'none',     // 'refresh', 'redirect'
            afterAssignOpen:        true,
            afterAssignOpenUrl:     null,
            afterAssignOpenStyle:   'js'        // 'link'
        },

        /**
         * List of themes and stores that assigned to them
         *
         * @type {Object.<number, Array>}
         * @private
         */
        _storesByThemes: [],

        /**
         * Form creation
         * @protected
         */
        _create: function() {
            this._setAssignedStores(this.options.storesByThemes);
            this._bind();
        },

        /**
         * Bind handlers
         * @protected
         */
        _bind: function() {
            //this.element is <body>
            this.element.on(this.options.assignEvent, $.proxy(this._onAssign, this));
            this.element.on(this.options.assignConfirmEvent, $.proxy(this._onAssignConfirm, this));
            this.element.on(this.options.loadEvent, $.proxy(function() {
                this.element.trigger('contentUpdated');
            }, this));
            this.element.on(this.options.beforeShowStoresEvent, $.proxy(this._onBeforeShowStoresEvent, this));
            //@TODO Remake bindings above to this._on()
        },

        /**
         * Handler for 'assign' event
         *
         * This event can be triggered from different locations:
         *  - Store Designer
         *  - VDE
         *  - VDE when trying to perform save-and-assign
         *
         * @param {Object.<string>} event
         * @param {Object.<string>} data
         * @private
         */
        _onAssign: function(event, data) {
            this.element.trigger(this.options.beforeShowStoresEvent, {theme_id: data.theme_id});

            var dialog = data.dialog = this._getDialog().data('dialog');    //@TODO WHY we need to keep dialog object?
            dialog.messages.clear();
            dialog.title.set($.mage.__('Assign to a Store View'));
            if (data.confirm_message) {
                dialog.text.set(data.confirm_message);
            }
            var buttons = data.confirm_buttons || {
                text: BUTTON_ASSIGN,
                click: $.proxy(function() {
                    $('body').trigger(this.options.assignConfirmEvent, {theme_id: data.theme_id});
                }, this),
                'class': 'primary'
            };
            dialog.setButtons(buttons);
            dialog.open();
        },

        /**
         * Handler for 'assign-confirm' event
         *
         * @param {Object.<string>} event
         * @param {Object.<string>} data
         * @private
         */
        _onAssignConfirm: function(event, data) {
            var themeId = data.theme_id;
            var stores = null;
            var dialog = this._getDialog().data('dialog');

            if (this.options.hasMultipleStores) {
                stores = this._getStoresFromCheckboxes();
                if (!this._isStoreChanged(themeId, stores)) {
                    dialog.messages.set($.mage.__('No stores were reassigned.'), 'info');
                    return;
                }
            }

            this._sendAssignRequest(themeId, stores, data.isSaveAndAssign);
            dialog.close();
        },

        /**
         * Handler for show-stores-before event
         *
         * @param {Object.<string>} event
         * @param {Object.<string>} data
         * @private
         */
        _onBeforeShowStoresEvent: function(event, data) {
            if (this.options.hasMultipleStores) {
                this._setThemeStoresToCheckboxes(data.theme_id);
            }
        },

        /**
         * Get stores that given theme is assigned to
         *
         * @param {number} theme
         * @returns {Array}
         * @private
         */
        _getAssignedStores: function(theme) {
            return this._storesByThemes[theme] || [];
        },

        /**
         * Setter for internal list of stores that themes assigned to
         *
         * @param {number|Object.<number, Array>} theme
         * @param {Array} stores
         * @private
         */
        _setAssignedStores: function(theme, stores) {
            if (arguments.length == 1) {
                this._storesByThemes = arguments[0];
            } else {
                this._storesByThemes[theme] = stores;
            }
        },

        /**
         * Get list of checked store-view identifiers
         *
         * This function only has sense when this.options.hasMultipleStores=true
         *
         * @returns {Array.<number>}
         * @private
         */
        _getStoresFromCheckboxes: function () {
            return this._getCheckboxes();
        },

        /**
         * Set checkboxes so they match stores that have given theme assigned
         *
         * @param {number} themeId
         * @private
         */
        _setThemeStoresToCheckboxes: function (themeId) {
            this._setCheckboxes(this._getAssignedStores(themeId));
        },

        /**
         * Check if the stores changed
         *
         * @param {number} themeId
         * @param {Array.<number>} storesToAssign
         * @protected
         */
        _isStoreChanged: function(themeId, storesToAssign) {
            var assignedStores = this._getAssignedStores(themeId);
            return !(storesToAssign.length === assignedStores.length &&
                $(storesToAssign).not(assignedStores).length === 0);
        },

        /**
         * Send AJAX request to assign theme to store-views
         *
         * @param {number} themeId
         * @param {Array.<number>|null} stores
         * @param {boolean} isSaveAndAssign
         * @public
         */
        _sendAssignRequest: function(themeId, stores, isSaveAndAssign) {
            if (!this.options.assignUrl) {
                throw Error($.mage.__('Url to assign themes to store is not defined'));
            }

            var data = {
                theme_id: themeId
            };
            if (stores === null) {
                data.stores = DEFAULT_STORE;
            } else if (stores.length === 0) {
                data.stores = EMPTY_STORES;
            } else {
                data.stores = stores;
            }

            // This is backend page standard messages container.
            $('#messages').html('');
            $.ajax({
                type: 'POST',
                url:  this.options.assignUrl,
                data: data,
                dataType: 'json',
                showLoader: true,
                success: $.proxy(function(response) {
                    this.assignThemeSuccess(response, stores, themeId, isSaveAndAssign);
                }, this),
                error: function() {
                    var dialog = this._getDialog().data('dialog');
                    var message = $.mage.__('Unknown error.');
                    if (isSaveAndAssign) {
                        dialog.messages.add(message, 'error');
                    } else {
                        dialog.messages.set(message, 'error');
                    }
                }
            });
        },

        /**
         * Assign Save Theme AJAX call Success handler
         *
         * @param {Object} response
         * @param {Array} stores
         * @param {number} themeId
         * @param {boolean} isSaveAndAssign
         */
        assignThemeSuccess: function(response, stores, themeId, isSaveAndAssign) {
            var url;
            var defaultStore = 0;
            var store = stores ? stores[0] : defaultStore;
            var dialog = this._getDialog().data('dialog');
            var messageType = response.error ? 'error' : 'success';

            if (isSaveAndAssign) {
                dialog.messages.add(response.message, messageType);
            } else {
                //dialog.messages.set(response.message, messageType);
            }

            if (response.error) {
                return;
            }

            this._setAssignedStores(themeId, stores);
            var o = this.options;
            switch (o.actionOnAssign) {
                case 'redirect':
                    if (o.afterAssignRedirectUrl != null) {
                        url = this._buildUrl(o.afterAssignRedirectUrl, store, response.themeId);
                        document.location = url;
                        setTimeout(function(){$('body').loader()}, 500);

                    }
                    break;

                case 'refresh':
                    document.location.reload();
                    setTimeout(function(){$('body').loader()}, 500);
                    break;

                default:
                    // do nothing
                    break;
            }

            if (!o.afterAssignOpen || o.afterAssignOpenUrl == null) {
                return;
            }

            url = this._buildUrl(o.afterAssignOpenUrl, store, response.themeId);
            switch (o.afterAssignOpenStyle) {
                case 'js':
                    window.open(url);
                    break;

                case 'link':
                    dialog.removeButton(BUTTON_ASSIGN);
                    dialog.text.clear();
                    dialog.addButton({
                        text: BUTTON_EDIT,
                        click: $.proxy(function() {
                            alert('Not implemented');
                        }, this),
                        'class': 'primary'

                    }, 0);
                    break;

                default:
                    throw Error('Invalid value of option "afterAssignOpenStyle"="'
                        + o.afterAssignOpenStyle + '"'
                    );
                    break;
            }
        },

        /**
         * Build URL that contains store_id and theme_id
         *
         * @param {string} base
         * @param {number} themeId
         * @param {number} storeId
         * @returns {string}
         * @private
         */
        _buildUrl: function(base, storeId, themeId) {
            var url = [
                base + 'store_id',
                storeId,
                'theme_id',
                themeId
            ].join('/');

            return url;
        },

        /**
         * Prepare items for post request
         *
         * @param {Object} items
         * @return {Object}
         * @private
         */
        _preparePostItems: function(items) {
            var postData = {};
            $.each(items, function(index, item){
                postData[index] = item.getPostData();
            });
            return postData;
        },

        /**
         * Get the IDs of those stores-views, whose checkboxes are set in the popup.
         *
         * @returns {Array.<number>}
         * @private
         */
        _getCheckboxes: function() {
            var stores = [];
            var checkedValue = 1;
            this._getDialog().find('form').serializeArray().each(function(object, index) {
                if (parseInt(object.value, 10) === checkedValue) {
                    stores.push(parseInt(object.name.match('storeviews\\[(\\d+)\\]')[1], 10));
                }
            });

            return stores;
        },

        /**
         * Set checkboxes according to array passed
         *
         * @param {Array.<number>} stores
         * @private
         */
        _setCheckboxes: function(stores) {
            this._getDialog().find('input[type=checkbox]').each(function(index, element) {
                element = $(element);

                var storeViewId = parseInt(element.attr('id').replace('storeview_', ''), 10);
                var isChecked = !(!stores || stores.indexOf(storeViewId) === -1);
                element.attr('checked', isChecked);
            });
        },

        /**
         * Get dialog element
         *
         * @returns {*|HTMLElement}
         * @private
         */
        _getDialog: function() {
            var selector = this.options.hasMultipleStores
                ? this.options.dialogSelectorMS : this.options.dialogSelector;
            return $(selector);
        }
    });

})(jQuery);
