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
    'use strict';
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

    /**
     * Multiple Stores Dialog
     */
    $.widget('vde.multipleStoresDialog', $.vde.dialog, {
        options: {
            autoOpen:    false,
            modal:       true,
            width:       570,
            dialogClass: 'vde-dialog'
        },

        /**
         * List of stores for current selected theme
         *
         * @var {Array.<number>}
         */
        _defaultStores: undefined,

        /**
         * Set stores to dialog related to theme
         *
         * @param stores
         * @returns {*}
         */
        setStores: function(stores) {
            this._defaultStores = stores;
            this._setCheckboxes(stores);
            this._setDefaultContent();
            return this;
        },

        /**
         * Get selected stores
         *
         * @returns {Array.<number>}
         */
        getStores: function() {
            return this._getCheckboxes();
        },

        /**
         * Callback event when we click on 'assign'
         *
         * @returns {*}
         */
        assignBefore: function() {
            this._checkChangesInfo();
            return this;
        },

        /**
         * Callback for after assign action
         *
         * @param response
         * @returns {*}
         */
        assignAfter: function(response) {
            var messageType = response.error ? 'error' : 'success';
            this.messages.add(response.message, messageType);
            return this;
        },

        /**
         * Check changes, were they be made (selected other stores)
         *
         * @private
         */
        _checkChangesInfo: function() {
            if (!this._isStoreChanged()) {
                this.messages.set($.mage.__('No stores were reassigned.'), 'info');
            }
        },

        /**
         * Set to dialog default content
         *
         * @private
         */
        _setDefaultContent: function() {
            this.title.set($.mage.__('Assign theme to your live store-view:'));
            this.messages.clear();
        },

        /**
         * Set checkboxes according to array passed
         *
         * @param {Array.<number>} stores
         * @private
         */
        _setCheckboxes: function(stores) {
            this.element.find('input[type=checkbox]').each(function(index, element) {
                element = $(element);

                var storeViewId = parseInt(element.attr('id').replace('storeview_', ''), 10);
                var isChecked = !(!stores || stores.indexOf(storeViewId) === -1);
                element.attr('checked', isChecked);
            });
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
            this.element.find('form').serializeArray().each(function(object, index) {
                if (parseInt(object.value, 10) === checkedValue) {
                    stores.push(parseInt(object.name.match('storeviews\\[(\\d+)\\]')[1], 10));
                }
            });
            return stores;
        },

        /**
         * Check if the stores changed
         *
         * @protected
         */
        _isStoreChanged: function() {
            var currentStores = this.getStores();
            var defaultStores = this._defaultStores;
            return !(currentStores.length === defaultStores.length &&
                $(currentStores).not(defaultStores).length === 0);
        }
    });


    /**
     * Single Store Dialog
     */
    $.widget('vde.singleStoreDialog', $.vde.dialog, {
        options: {
            autoOpen:    false,
            modal:       true,
            width:       570,
            dialogClass: 'vde-dialog'
        },

        /**
         * Set stores to dialog related to theme
         *
         * @returns {*}
         */
        setStores: function() {
            this._setDefaultContent();
            return this;
        },

        /**
         * Get selected stores
         *
         * @returns {Array.<number>|number}
         */
        getStores: function() {
            return DEFAULT_STORE;
        },

        /**
         * Callback event when we click on 'assign'
         *
         * @returns {*}
         */
        assignBefore: function() {
            return this;
        },

        assignAfter: function(response) {
            this.close();
            document.location.reload();
            return this;
        },

        _setDefaultContent: function() {
            this.title.set($.mage.__('Assign to a Store View'));
        }
    });

    /**
     * VDE assign theme widget
     */
    $.widget('vde.themeAssign', {
        options: {
            beforeShowStoresEvent: 'show-stores-before',
            dialogSelectorMS:      '#dialog-message-assign',
            dialogSelector:        '#dialog-message-assign-single',
            closePopupBtn:         '[class^="action-close"]',
            assignUrl:             null,
            afterAssignUrl:        null,
            storesByThemes:        [],
            hasMultipleStores:     null,
            redirectOnAssign:      false,
            refreshOnAssign:       true,
            afterAssignMode:       'navigation'
        },

        _dialog: undefined,

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

            if (false && this.options.hasMultipleStores) {
                this._dialog = $(this.options.dialogSelectorMS).multipleStoresDialog().data('multipleStoresDialog');
            } else {
                this._dialog = $(this.options.dialogSelector).singleStoreDialog().data('singleStoreDialog');
            }

            this._setAssignedStores(this.options.storesByThemes);
            this._bind();
        },

        /**
         * Bind handlers
         * @protected
         */
        _bind: function() {
            this._on({
                'assign': '_onAssign',
                'loaded': function() {
                    this.element.trigger('contentUpdated');
                }
            });
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
            this._dialog.setStores(this._getAssignedStores(data.theme_id));

            if (data.confirm_message) {
                this._dialog.text.set(data.confirm_message);
            }

            var buttons = data.confirm_buttons || {
                text: BUTTON_ASSIGN,
                click: $.proxy(function(event) {
                    var button = $(event.currentTarget);
                    if (!button.hasClass('disabled')) {
                        button.addClass('disabled');
                        this._sendAssignRequest(data.theme_id, this._dialog.getStores(), true);
                    }
                    return false;
                }, this),
                'class': 'primary'
            };
            this._dialog.setButtons(buttons);
            this._dialog.open();
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
         * Send AJAX request to assign theme to store-views
         *
         * @param {number} themeId
         * @param {Array.<number>|null} stores
         * @param {boolean} isSaveAndAssign
         * @protected
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
            this._dialog.assignBefore();
            $.ajax({
                type: 'POST',
                url:  this.options.assignUrl,
                data: data,
                dataType: 'json',
                showLoader: true,
                success: $.proxy(function(response) {
                    this._dialog.assignAfter(response);
                    this.assignThemeSuccess(response, stores, themeId);
                }, this),
                error: function() {
                    var message = $.mage.__('Unknown error.');
                    if (isSaveAndAssign) {
                        this._dialog.messages.add(message, 'error');
                    } else {
                        this._dialog.messages.set(message, 'error');
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
         */
        assignThemeSuccess: function(response, stores, themeId) {
            if (!response.error) {
                this._setAssignedStores(themeId, stores);
                if (this.options.redirectOnAssign && this.options.afterAssignUrl != null) {
                    document.location = this.options.afterAssignUrl;
                }
            }
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
        }
    });

})(jQuery);
