/**
 * {license_notice}
 *
 * @category    Rma
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function ($) {
    $.widget('mage.rmaCreate', {

        /**
         * options with default values
         */
        options: {
            template_registrant: '#template-registrant',
            registrant_options: '#registrant-options',
            add_item_to_return: 'add-item-to-return',
            btn_remove: 'btn-remove',
            qty_requested_block: '#qty_requested_block',
            remaining_quantity_block: '#remaining_quantity_block',
            remaining_quantity: '#remaining_quantity',
            reason_other: '#reason_other',
            items_reason_other: '#items:reason_other',
            row: '#row',
            add_row: 'add-row',
            radio_item: '#radio:item',
            order_item_id: '#item:order_item_id',
            items_item: 'items:item',
            items_reason: 'items:reason',
            liIndex: 0,
            formDataPost: null,
            firstItemId: null,
            productType: null,
            availableQuantity: 0,
            prodTypeBundle: null
        },

        /**
         * Initialize and attach event callbacks for adding and deleting RMA tracking rows
         * @private
         */
        _create: function () {
            //On document ready related tasks
            $($.proxy(this._ready, this));
        },

        /**
         * Process and loop thru all form data to create "Items to return" with preselected value. This is used for failed submit.
         * For first time this will add a default row without remove icon/button
         *
         * @private
         */
        _ready: function () {
            this._processFormDataArr(this.options.formDataPost);
            //If no form data , then add default row for Return Item
            if (this.options.liIndex == 0) {
                this._addRegistrant();
            }
        },

        /**
         * Parse form data and re-create the return item information row preserving the submitted values
         * @param {Object} formDataArr
         * @private
         */
        _processFormDataArr: function (formDataArr) {
            if (formDataArr) {
                for (var i = 0; i < formDataArr.length; i++) {
                    //Add a row
                    this._addRegistrant();
                    //Set the previously selected values
                    for (var key in formDataArr[i]) {

                        if (!key)
                            continue;

                        if (key === 'order_item_id') {
                            this._setFieldById(this.options.items_item + i, formDataArr[i][key]);
                            this._showBundle(i, formDataArr[i][key]);
                            this._setFieldById(this.options.order_item_id.substring(1) + i + '_' + formDataArr[i][key]);
                        } else if (key === 'items') {
                            for (var itemKey in formDataArr[i][key]) {
                                this._setFieldById('items[' + i + '][' + formDataArr[i]['order_item_id'] + '][checkbox][item][' + itemKey + ']');
                                this._setFieldById('items[' + i + '][' + formDataArr[i]['order_item_id'] + '][checkbox][qty][' + itemKey + ']', formDataArr[i][key][itemKey]);
                                this._setBundleFieldById(itemKey, formDataArr[i]['order_item_id'], i);
                                delete formDataArr[i]['qty_requested'];
                            }
                        } else if (key === 'qty_requested' && formDataArr[i][key] !== "") {
                            this._setFieldById('items:' + key + i, formDataArr[i][key]);
                        } else {
                            this._setFieldById('items:' + key + i, formDataArr[i][key]);
                            if (key === 'reason') {
                                this._showOtherOption(formDataArr[i][key], i);
                            }
                        }
                    }
                }
            }
        },

        /**
         * Add new return item information row using the template
         * @private
         */
        _addRegistrant: function () {

            var li = this._setUpTemplate(this.options.liIndex, this.options.template_registrant, this.options.registrant_options);
            this._showBundle(this.options.liIndex, this.options.firstItemId);
            this._showQuantity(this.options.productType, this.options.liIndex, this.options.availableQuantity);
            //Increment after rows are added
            this.options.liIndex++;
        },

        /**
         * Remove return item information row
         * @param {string} liIndex - return item information row index
         * @return {boolean}
         * @private
         */
        _removeRegistrant: function (liIndex) {
            $(this.options.row + liIndex).remove();
            return false;
        },

        /**
         *
         * @param {string} index - return item information row bundle index
         * @param {string} itemId - bundle item id
         * @return {Boolean}
         * @private
         */
        _showBundle: function (index, itemId) {

            $('div[id^="radio\\:item' + index + '_"]').each(function () {
                var $this = $(this);
                if ($this.attr('id')) {
                    $this.parent().hide();
                }
            })

            $('input[id^="items[' + index + ']"]').each(function () {
                this.disabled = true;
            })

            var rItem = this._esc(this.options.radio_item) + index + '_' + itemId;
            var rOrderItemId = this._esc(this.options.order_item_id) + index + '_' + itemId;

            if ($(rItem).length) {
                $(rItem).parent().show();
                this._enableBundle(index, itemId);
            }

            if ($(rOrderItemId).length) {
                var typeQty = $(rOrderItemId).attr('rel');
                var position = typeQty.lastIndexOf('_');
                this._showQuantity(typeQty.substring(0, position), index, typeQty.substr(position + 1))
            }
        },

        /**
         *
         * @param {string} type - product type
         * @param {string} index - return item information row index
         * @param {string} qty - quantity of item specified
         * @private
         */
        _showQuantity: function (type, index, qty) {
            var qtyReqBlock = $(this.options.qty_requested_block + '_' + index),
                remQtyBlock = $(this.options.remaining_quantity_block + '_' + index),
                remQty = $(this.options.remaining_quantity + '_' + index);

            if (type === this.options.prodTypeBundle) {
                if (qtyReqBlock.length) {
                    qtyReqBlock.hide();
                }
                if (remQtyBlock.length) {
                    remQtyBlock.hide();
                }
            } else {
                if (qtyReqBlock.length) {
                    qtyReqBlock.show();
                }
                if (remQtyBlock.length) {
                    remQtyBlock.show();
                }
                if (remQty.length) {
                    remQty.text(qty)
                }
            }
        },

        /**
         * Enable bundle and its items
         * @param {string} index - return item information row index
         * @param {string} bid - bundle type id
         * @private
         */
        _enableBundle: function (index, bid) {
            $('input[id^="items[' + index + '][' + bid + '][checkbox][item]["]').each(function () {
                this.disabled = false;
            });
            $('input[id^="items[' + index + '][' + bid + '][checkbox][qty]["]').each(function () {
                if (this.value) {
                    this.disabled = false;
                }
            });
        },

        /**
         * Set the value on given element
         * @param {string} domId
         * @param {string} value
         * @private
         */
        _setFieldById: function (domId, value) {
            x = $('#' + this._esc(domId));
            if (x.length) {
                if (x.attr('type') === 'checkbox') {
                    x.attr('checked', true);
                } else if (x.is('option')) {
                    x.attr('selected', 'selected');
                } else {
                    x.val(value);
                }
            }
        },

        /**
         *
         * @param id {string}
         * @param bundleID {string}
         * @param index {string} - return item information row index
         * @private
         */
        _setBundleFieldById: function (id, bundleID, index) {
            this._showBundle(index, bundleID);
            this._showBundleInput(id, bundleID, index);
            this._showQuantity('bundle', index, 0);
        },

        /**
         * Toggle "Other" options
         * @param value
         * @param index - return item information row index
         * @private
         */
        _showOtherOption: function (value, index) {
            var resOther = this.options.reason_other;
            var iResOther = this._esc(this.options.items_reason_other);
            if (value === 'other') {
                $(resOther + index).show();
                $(iResOther + index).attr('disabled', false);
            } else {
                $(resOther + index).hide();
                $(iResOther + index).attr('disabled', true);
            }
        },

        /**
         * Toggle bundled products
         * @param {string} id - bundle id
         * @param {string} bid - bundle type id
         * @param {string} index - return item information row index
         * @private
         */
        _showBundleInput: function (id, bid, index) {
            var qty = this._esc('#items[' + index + '][' + bid + '][checkbox][qty][' + id + ']');
            if ($(this._esc('#items[' + index + '][' + bid + '][checkbox][item][' + id + ']')).is(':checked')) {
                $(qty).show();
                $(qty).attr('disabled', false);
            } else {
                $(qty).hide();
                $(qty).attr('disabled', true);
            }
        },

        /**
         * Initialize and create markup for Return Item Information row
         * using the template
         * @param {string} index - current index/count of the created template. This will be used as the id
         * @param {string} templateId - template markup selector
         * @param {string} containerId - container where the template will be injected
         * @return {*}
         * @private
         */
        _setUpTemplate: function (index, templateId, containerId) {
            var li = $('<li></li>')
            li.addClass('fields').attr('id', 'row' + index);
            $(templateId).tmpl([
                {_index_: index}
            ]).appendTo(li);
            $(containerId).append(li);
            // skipping first row
            if (index != 0) {
                li.addClass(this.options.add_row);
            } else {
                //Hide the close button for first row
                $('#' + this.options.btn_remove + '0').hide();
            }
            //Binding template-wide events handlers
            this.element.on('click', 'a,input:checkbox', $.proxy(this._handleClick, this));
            this.element.on('change', 'select', $.proxy(this._handleChange, this));

            return li;
        },

        /**
         * Delegated handler for click
         * @param {Object} e - Native event object
         * @private
         */
        _handleClick: function (e) {
            var currElem = $(e.currentTarget);

            if (currElem.attr('id') === this.options.add_item_to_return) {
                if (e.handled !== true) {
                    this._addRegistrant();
                    e.handled = true;
                    return false;
                }

            } else if (currElem.hasClass(this.options.btn_remove)) {
                //Extract index
                this._removeRegistrant(currElem.parent().attr('id').replace(this.options.btn_remove, ''));
                return false;

            } else if (currElem.attr('type') === 'checkbox') {
                var args = currElem.data("args");
                if (args) {
                    this._showBundleInput(args['item'], args['bundleId'], args['index']);
                }
            }
        },

        /**
         * Delegated handler for change
         * @param {Object} e - Native event object
         * @private
         */
        _handleChange: function (e) {
            var currElem = $(e.currentTarget);
            var currId = currElem.attr('id');
            var args = currElem.data("args");
            if (args && currId) {
                if (currId.substring(0, 10) === this.options.items_item) {
                    this._showBundle(args['index'], currElem.val());
                } else if (currId.substring(0, 12) === this.options.items_reason) {
                    this._showOtherOption(currElem.val(), args['index']);
                }
                return false;
            }
        },

        /**
         * Utility function to add escape chars for jquery selector strings
         * @param str - string to be processed
         * @return {string}
         * @private
         */
        _esc: function (str) {
            if (str)
                return str.replace(/([ ;&,.+*~\':"!^$[\]()=>|\/@])/g, '\\$1')
            else
                return str;
        }
    });

})(jQuery);