/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function ($, window) {
    $.widget('mage.addressTabs', $.mage.tabs, {
        options: {
            itemCount: 0,
            baseItemId: 'new_item',
            loader: null,
            regionsUrl: null,
            defaultCountries: [],
            optionalZipCountries: [],
            requiredStateForCountries: [],
            countryElement: null,
            regionIdElement: null,
            regionElement: null,
            deleteConfirmPrompt: ''
        },

        _addNewAddress: function () {
            this.options.itemCount++;

            // prevent duplication of ids
            while (this.element.find("div[data-item=" + this.options.itemCount + "]").length) {
                this.options.itemCount++;
            }

            var formName = this.options.baseItemId + this.options.itemCount;

            // add the new address form
            this.element.find('.address-item-edit').append('<div id="' + 'form_' + formName +
                '" data-item="' + this.options.itemCount +
                '" class="address-item-edit-content" data-mage-init="{observableInputs:{\'name\': \'' + formName +
                '\'}}">' + this._prepareTemplate(this.element.find('div[data-template="address_form"]').html()) +
                '</div>');

            var newForm = $('#form_' + formName);

            // @TODO something different?
            var template = this._prepareTemplate(this.element.find('div[data-template="address_item"]').html())
                .replace('data_item_value_', '' + this.options.itemCount)
                .replace('delete_button', 'delete_button' + this.options.itemCount)
                .replace('form_new_item', 'form_new_item' + this.options.itemCount)
                .replace('address_item_', 'address_item_' + this.options.itemCount);

            // add the new address to the tabs list before the add new action list
            this.element.find('.address-list-actions').before(template);

            // refresh the widget to pick up the newly added tab.
            this.refresh();

            // activate the newly added tab
            this.option('active', -1);

            // @TODO Used in deleteAddress and cancelAdd?
            var newItem = $(formName);
            newItem.isNewAddress = true;
            newItem.formBlock = newForm;

            this.element.trigger('contentUpdated', newItem);

            // pre-fill form with account firstname, lastname, and country
            newForm.find(':input[data-ui-id="customer-edit-tab-addresses-fieldset-element-text-address-template-firstname"]')
                .val($(':input[data-ui-id="customer-edit-tab-account-fieldset-element-text-account-firstname"]').val());
            newForm.find(':input[data-ui-id="customer-edit-tab-addresses-fieldset-element-text-address-template-lastname"]')
                .val($(':input[data-ui-id="customer-edit-tab-account-fieldset-element-text-account-lastname"]').val());

            var accountWebsiteId = $(':input[data-ui-id="store-switcher-form-renderer-fieldset-element-select-account-website-id"]').val();
            if (accountWebsiteId !== '' && undefined !== this.options.defaultCountries[accountWebsiteId]) {
                newForm.find('customer-edit-tab-addresses-fieldset-element-form-field-country-id').val(this.options.defaultCountries[accountWebsiteId]);
            }

            // .val does not trigger change event, so manually trigger. (Triggering change of any field will handle update of all fields.)
            newForm.find(':input[data-ui-id="customer-edit-tab-addresses-fieldset-element-text-address-template-firstname"]').trigger("change");

            // @TODO this function
//            this.setActiveItem(newItem);

            this._bindCountryRegionRelation(newForm);
        },

        /**
         * This method is used to bind events associated with this widget.
         */
        _bind: function () {
            this._on(this.element.find(':button[data-ui-id="customer-edit-tab-addresses-add-address-button"]'),
                {'click': '_addNewAddress'});
            this._on({'formchange': '_updateAddress', 'dataItemDelete': '_deleteItemPrompt'});
            this._on(this.element.find('.countries'), {'change': '_onAddressCountryChanged'});
        },

        _create: function () {
            this._super();
            this._bind();
            this.options.loader = new varienLoader(true);
        },

        /**
         * This method deletes the item in the list.
         * @private
         */
        _deleteItem: function(dataItem) {
            // remove the elements from the page
            this.element.find('[data-item="' + dataItem + '"]').remove();

            // refresh the widget to pick up the removed tab
            this.refresh();
        },

        /**
         * This method prompts the user to confirm the deletion of the item in the list.
         * @private
         */
        _deleteItemPrompt: function(event, data) {
            if(window.confirm(this.options.deleteConfirmPrompt)){
                this._deleteItem(data.item);
            }
        },

        _prepareTemplate: function (template) {
            // @TODO Replace '_template_' with data-mage-init option <?php echo $_templatePrefix ?>
            return template
                .replace(/_template_/g, '_item' + this.options.itemCount)
                .replace(/_counted="undefined"/g, '')
                .replace(/"select_button_"/g, 'select_button_' + this.options.itemCount);
        },

        /**
         * This method is used to grab the data from the form and display it nicely.
         */
        _syncFormData: function (container) {
            if (container) {
                var data = {};

                $(container).find(':input').each(function (index, inputField) {
                    var id = inputField.id;
                    if (id) {
                        id = id.replace(/^(_item)?[0-9]+/, '');
                        id = id.replace(/^(id)?[0-9]+/, '');
                        var value = inputField.getValue();
                        var tagName = inputField.tagName.toLowerCase();
                        if (tagName == 'select') {
                            if (inputField.multiple) {
                                var values = $([]);
                                var l = inputField.options.length;
                                for (j = 0; j < l; j++) {
                                    var o = inputField.options[j];
                                    if (o.selected === true) {
                                        values[values.length] = o.text.escapeHTML();
                                    }
                                }
                                data[id] = values.join(', ');
                            } else {
                                var option = inputField.options[inputField.selectedIndex],
                                    text = option.value == '0' || option.value === '' ? '' : option.text;
                                data[id] = text.escapeHTML();
                            }
                        } else if (value !== null) {
                            data[id] = value.escapeHTML();
                        }
                    }
                });

                // Set name of state to 'region' if list of states are in 'region_id' selectbox
                if (!data.region && data.region_id) {
                    data.region = data.region_id;
                    delete data.region_id;
                }

                // Set data to html
                var itemContainer = this.element.find("[aria-selected='true'] address");
                if (itemContainer.length && itemContainer[0]) {
                    var html = $("<div/>").append($('[data-template="item-content-tmpl"]').tmpl(data)).html();
                    html = html.replace(new RegExp('(<br\\s*/?>\\s*){2,}', 'img'), '<br/>');
                    html = html.replace(new RegExp('<br\\s*/?>(\\s*,){1,}\\s*<br\\s*/?>', 'ig'), '<br/>');
                    html = html.replace(new RegExp('<br\\s*/?>(\\s*,){1,}(.*)<br\\s*/?>', 'ig'), '<br/>$2<br/>');
                    html = html.replace(new RegExp('<br\\s*/?>(.*?)(,\\s*){1,}<br\\s*/?>', 'ig'), '<br/>$1<br/>');
                    html = html.replace(new RegExp('<br\\s*/?>(.*?)(,\\s*){2,}(.*?)<br\\s*/?>', 'ig'), '<br/>$1, $3<br/>');
                    html = html.replace(new RegExp('t:\\s*<br\\s*/?>', 'ig'), '');
                    html = html.replace(new RegExp('f:\\s*<br\\s*/?>', 'ig'), '');
                    html = html.replace(new RegExp('vat:\\s*$', 'ig'), '');
                    itemContainer[0].innerHTML = html;
                }
            }
        },

        /**
         * This method processes the event associated with a form field changing.
         * @param event Event occurring.
         * @private
         */
        _updateAddress: function (event) {
            this._syncFormData(this._getFormContainer(event.target));
        },

        /**
         * This method returns the form containing this element.
         * @param element jQuery or DOM element.
         * @private
         */
        _getFormContainer: function(element) {
            if (!(element instanceof jQuery)) {
                element = $(element);
            }
            return element.closest(".address-item-edit-content");
        },

        /**
         * This method binds a country change event to _onAddressCountryChanged method.
         * @param formElement the form containing the country that changed.
         * @private
         */
        _bindCountryRegionRelation : function(formElement){
            this._on(formElement.find('.countries'), {'change': '_onAddressCountryChanged'});
        },

        /**
         * This method updates region input; and region and zipCode optional/required indicator based on the country.
         * @param event Change event occurring
         * @private
         */
        _onAddressCountryChanged : function(event){
            var countryElement = event.target;
            this.options.countryElement = countryElement;

            var formElement = $(countryElement).closest('.address-item-edit-content');
            var fieldElement = $(formElement).find('.field-region');
            var regionIdElement =  $(fieldElement).find('.input-text');
            this.options.regionIdElement = regionIdElement;
            this.options.regionElement = regionIdElement.next();

            if (countryElement.value) {
                // obtain regions for the country
                var url = this.options.regionsUrl + 'parent/' + countryElement.value;
                console.log(url);
                this.options.loader.load(url, {}, jQuery.proxy(this._refreshRegionField, this));
            } else {
                // Set empty text field in region
                this._refreshRegionField('[]');
            }
            // set Zip optional/required
            this._setPostcodeOptional(countryElement);
        },

        /**
         * This method updates the region input from the server response.
         * @param serverResponse Array of regions/state/provinces or empty if regions n/a for country
         * @private
         */
        _refreshRegionField : function(serverResponse){
            if (!serverResponse)
                return;
            var data = eval('(' + serverResponse + ')');

            var regionField = $(this.options.regionElement).closest('div.field');
            var newInput = null; // id of input that was added to a page - filled below
            var regionControl = regionField.find('.control');
            var regionInput = null;
            var regionIdInput = null;

            // clear current region input/select
            regionControl.empty();

            if (data.length) {
                // Create visible selectbox 'region_id' and hidden 'region'
                regionIdInput = $('<select>').attr({
                    name: this.options.regionIdElement.attr("name"),
                    id: this.options.regionIdElement.attr("id"),
                    class: "required-entry input-text select",
                    title: this.options.regionIdElement.attr("title")
                }).appendTo(regionControl);

                data.each(function(item) {
                    regionIdInput.append($("<option />").val(item.value).text(item.label));
                });

                // @TODO Set selected value
                regionIdInput.val(this.options.regionElement.val());

                regionInput = $('<input>').attr({
                    name: this.options.regionElement.attr("name"),
                    id: this.options.regionElement.attr("id"),
                    type: "hidden"
                }).appendTo(regionControl);

                regionField.addClass("required");

                newInput = regionIdInput;
            }
            else {
                // Create visible text input 'region' and hidden 'region_id'
                regionInput = $('<input>').attr({
                    type: "text",
                    name: this.options.regionElement.attr("name"),
                    id: this.options.regionElement.attr("id"),
                    class: "input-text",
                    title: this.options.regionElement.attr("title")
                }).appendTo(regionControl);

                regionIdInput = $('<input>').attr({
                    type: "hidden",
                    name: this.options.regionIdElement.attr("name"),
                    id: this.options.regionIdElement.attr("id")
                }).appendTo(regionControl);

                regionField.removeClass("required");

                newInput = regionInput;
            }

            this.options.regionElement = regionInput;
            this.options.regionIdElement = regionIdInput;

            // Updating in address info
            this._syncFormData(this._getFormContainer(newInput)); // Update address info now
            var activeElement = regionInput;
            if (('select' == $(regionIdInput).prop("tagName").toLowerCase()) && regionIdInput) {
                activeElement = regionIdInput;
            }

            activeElement.on('change', $.proxy(this._syncFormData, this, this._getFormContainer(activeElement)));
            this._checkRegionRequired([regionInput, regionIdInput], activeElement);
        },

        /**
         * This method updates the region input required/optional and validation classes.
         * @param elements Region elements
         * @param activeElementId Active Region element
         * @private
         */
        _checkRegionRequired: function(elements, activeElementId)
        {
            var label, wildCard;
            var that = this;
            var regionRequired = this.options.requiredStateForCountries.indexOf(this.options.countryElement.value) >= 0;

            elements.each(function(currentElement) {
                var form = $(currentElement).closest("form");
                var validationInstance = form ? jQuery(form).data('validation') : null;

                if (validationInstance) {
                    validationInstance.clearError(currentElement);
                }

                if (!regionRequired) {
                    if (currentElement.hasClass('required-entry')) {
                        currentElement.removeClass('required-entry');
                    }
                    if ('select' == currentElement.prop("tagName").toLowerCase() &&
                            currentElement.hasClass('validate-select')) {
                        currentElement.removeClass('validate-select');
                    }
                } else if (activeElement == currentElement) {
                    if (!currentElement.hasClass('required-entry')) {
                        currentElement.addClass('required-entry');
                    }
                    if ('select' == currentElement.prop("tagName").toLowerCase() &&
                            !currentElement.hasClass('validate-select')) {
                        currentElement.addClass('validate-select');
                    }
                }
            });
        },

        /**
         * This method updates the zip/postal code required/optional indicator.
         * @param countryElement
         * @private
         */
        _setPostcodeOptional: function(countryElement) {
            var formElement = $(countryElement).closest('.address-item-edit-content');
            var fieldElement = $(formElement).find('.field-postcode');
            var zipElement = $(fieldElement).find('.input-text');

            var zipField = $(zipElement).closest('.field-postcode');
            if (this.options.optionalZipCountries.indexOf(countryElement.value) != -1) {
                if ($(zipElement).hasClass('required-entry')) {
                    $(zipElement).removeClass('required-entry');
                }
                $(zipField).removeClass('required');
            } else {
                $(zipElement).addClass('required-entry');
                $(zipField).addClass('required');
            }
        }
    });

    $.widget('mage.observableInputs', {
        options: {
            name: ''
        },

        /**
         * This method is used to bind events associated with this widget.
         */
        _bind: function () {
            this._on(this.element.find(':input'), {'change': '_triggerChange'});
        },

        _create: function () {
            this._super();
            this._bind();
        },

        /**
         * This method is used to trigger a change element for a given entity.
         */
        _triggerChange: function (element) {
            // send the name of the captor and the field that changed
            this.element.trigger('formchange', {'name': this.options.name, 'element': element.target});
        }
    });

    /**
     * This widget is used to trigger a message to delete a data item (i.e. D of CRUD).
     */
    $.widget('mage.dataItemDeleteButton', {
        options: {
            item: ''
        },

        /**
         * This method is used to bind events associated with this widget.
         */
        _bind: function () {
            this._on(this.element.find('[data-role="delete"]'), {'click': '_triggerDelete'});
        },

        _create: function () {
            this._super();
            this._bind();

            // if the item was not specified, find the data-item element wrapper
            if (this.options.item.length === 0) {
                var dataItemContainer = this.element.parents('[data-item]');
                if (dataItemContainer.length === 1) {
                    this.options.item = dataItemContainer.attr("data-item");
                }
            }
        },

        /**
         * This method is used to trigger a delete message for this item.
         */
        _triggerDelete: function () {
            // send the name of the captor and the field that changed
            this.element.trigger('dataItemDelete', {'item': this.options.item});

            // we are handling the click, so stop processing
            return false;
        }
    });
})(jQuery, window);