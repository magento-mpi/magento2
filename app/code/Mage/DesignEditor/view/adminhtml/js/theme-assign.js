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
     * VDE assign theme widget
     */
    $.widget('vde.themeAssign', {
        options: {
            assignEvent:        'assign',
            assignConfirmEvent: 'assign-confirm',
            loadEvent:          'loaded',
            dialogSelector:     '#dialog-message-assign',
            dialogSelectorSsm:  '#dialog-message-assign-ssm',
            closePopupBtn:      '[class^="action-close"]',
            assignUrl:          null,
            afterAssignUrl:     null,
            storesByThemes:     [],
            isMultipleStoreViewMode: null,
            redirectOnAssign:   false,
            openNewOnAssign:    true,
            refreshOnAssign:    true
        },

        /**
         * Identifier of a theme currently processed
         */
        themeId: null,          //@TODO try to remove usage of themeId by passing it to method directly as param

        /**
         * List of themes and stores that assigned to them
         *
         * @type {Array.<number>}
         */
        storesByThemes: [],

        /**
         * Form creation
         * @protected
         */
        _create: function() {
            this.storesByThemes = this.options.storesByThemes;
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
        },

        /**
         * Handler for 'assign' event
         *
         * @param event
         * @param data
         * @private
         */
        _onAssign: function(event, data) {
            this.themeId = data.theme_id;
            if (this.options.isMultipleStoreViewMode) {
                var stores = this.storesByThemes[data.theme_id] || [];
                this._setCheckboxes(stores);
            }

            var assignConfirmEvent = this.options.assignConfirmEvent;

            var dialog = this._getDialog();
            data.dialog = dialog;
            dialog.find('.messages').html('');
            var buttons = data.confirm_buttons || [
                {
                    text: $.mage.__('Assign'),
                    click: function() {
                        $('body').trigger(assignConfirmEvent);
                    },
                    'class': 'primary'
                },
                {
                    text: $.mage.__('Close'),
                    click: function() {
                        $(this).dialog('close');
                    },
                    'class': 'action-close'
                }
            ];
            dialog.dialog('option', 'buttons', buttons);
            if (data.confirm_message) {
                dialog.find('.confirm_message').html(data.confirm_message);
            }

            dialog.dialog('open');
        },

        /**
         * Handler for 'assign-confirm' event
         *
         * @private
         */
        _onAssignConfirm: function() {
            var stores = this._getCheckboxes();
            var dialog = this._getDialog();

            if (this.options.isMultipleStoreViewMode && !this._isStoreChanged(this.themeId, stores)) {
                var message = [
                    '<div class="message message-error">',
                    $.mage.__('No stores were reassigned.'),
                    '</div>'
                ].join('');
                dialog.find('.messages').html(message);
                return;
            }

            this.sendAssignRequest(this.themeId, stores);
            this.themeId = null;
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
         * Check if the stores changed
         * @protected
         */
        _isStoreChanged: function(themeId, storesToAssign) {
            var assignedStores = this.options.storesByThemes[themeId] || [] ;
            return !(storesToAssign.length === assignedStores.length &&
                $(storesToAssign).not(assignedStores).length === 0);
        },

        /**
         * Send AJAX request to assign theme to store-views
         * @public
         */
        sendAssignRequest: function(themeId, stores) {
            if (!this.options.assignUrl) {
                throw Error($.mage.__('Url to assign themes to store is not defined'));
            }

            var data = {
                theme_id: themeId,
                stores:   stores
            };
            //TODO since we can't convert data to JSON string we use magic numbers
            var DEFAULT_STORE = '-1';
            var EMPTY_STORES = '-2';
            if (data.stores === null) {
                data.stores = DEFAULT_STORE;
            } else if (data.stores.length === 0) {
                data.stores = EMPTY_STORES;
            }

            $('#messages').html('');
            $.ajax({
                type: 'POST',
                url:  this.options.assignUrl,
                data: data,
                dataType: 'json',
                success: $.proxy(function(response) {
                    this.assignThemeSuccess(response, stores, themeId);
                }, this),
                error: function() {
                    alert($.mage.__('Error: unknown error.'));
                }
            });
        },

        /**
         * Assign Save Theme AJAX call Success handler
         *
         * @param response
         * @param stores
         * @param themeId
         */
        assignThemeSuccess: function(response, stores, themeId) {
            var dialog = this._getDialog();
            if (response.error) {
                var message = [
                    '<div class="message message-error">',
                    $.mage.__('Error'), ': "', response.message, '".',
                    '</div>'
                ];
            } else {
                var message = [
                    '<div class="message-success">',
                    response.success,
                    '</div>'
                ];
                if (this.options.redirectOnAssign && this.options.afterAssignUrl != null) {
                    var defaultStore = 0;
                    var url = [
                        this.options.afterAssignUrl + 'store_id',
                        stores ? stores[0] : defaultStore,
                        'theme_id',
                        response.themeId
                    ].join('/');
                    this.storesByThemes[themeId] = stores;

                    if (this.options.openNewOnAssign) {
                        window.open(url);
                    } else {
                        document.location = url;
                    }
                }
            }
            dialog.find('.messages').html(dialog.find('.messages').html() + message.join(''));
        },

        /**
         * Prepare items for post request
         *
         * @param items
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
            var selector = this.options.isMultipleStoreViewMode
                ? this.options.dialogSelector : this.options.dialogSelectorSsm;
            return $(selector);
        }
    });

})(jQuery);
