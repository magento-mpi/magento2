/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function($) {
    $.widget('mage.addressTabs', $.mage.tabs, {
        options: {
            tabLabel: 'tabs-',
            itemCount: 0,
            baseItemId: 'new_item',
            // @TODO obtain default countries
            defaultCountries: null
        },

        _addNewAddress: function(){
            this.options.itemCount++;

            // preventing duplication of ids
            while ($("form_address_item_" + this.options.itemCount).length) {
                this.options.itemCount++;
            }

            $('.address-item-edit').append('<div id="' + 'form_' + this.options.baseItemId + this.options.itemCount + '" class="address-item-edit-content">'
                + this._prepareTemplate($('#address_form_template').html())
                + '</div>');

            var newForm = $('#form_' + this.options.baseItemId + this.options.itemCount);

            $('#_item' + this.options.itemCount + 'firstname').val($('#_accountfirstname').val());
            $('#_item' + this.options.itemCount + 'lastname').val($('#_accountlastname').val());

            // @TODO something different?
            var template = this._prepareTemplate($('#address_item_template').html())
                .replace('delete_button', 'delete_button' + this.options.itemCount)
                .replace('form_new_item', 'form_new_item' + this.options.itemCount)
                .replace('address_item_', 'address_item_' + this.options.itemCount);

            $('.address-list-actions').before(template);
            this.refresh();
            this.select(this.options.itemCount - 1);

            // @TODO Used in deleteAddress and cancelAdd?
            var newItem = $(this.options.baseItemId + this.options.itemCount);
            newItem.isNewAddress = true;
            newItem.formBlock = newForm;

            // @TODO need to bind events?
//            this.addItemObservers(newItem);
            // @TODO this function
//            this.setActiveItem(newItem);
            // @TODO country/region relationship
//          this.bindCountryRegionRelation(newForm.id);

            // @TODO all this
            /*            if ($('#_accountwebsite_id').val !== ''
             && undefined !== this.options.defaultCountries[$('#_accountwebsite_id').val]
             ) {
             $('#_item' + this.options.itemCount + 'country_id').val = this.options.defaultCountries[$('#_accountwebsite_id').val];
             $('#_item' + this.options.itemCount + 'country_id').trigger('change');
             }

             if( $('#_item'+this.options.itemCount+'firstname').val )
             this.syncFormData($('#_item'+this.options.itemCount+'firstname'));
             if( $('#_item'+this.options.itemCount+'lastname').val )
             this.syncFormData($('#_item'+this.options.itemCount+'lastname')); */
        },

        /**
         * This method is used to add new address elements to the form.
         */
        _addNewAddressTemp: function() {
            var index = this._getMaxIndex() + 1;
            var newTabId = this.options.tabLabel + index;

            // add the new address to the tabs list before the add new action list
            this.element.find('.address-list-actions').before('<li class="address-list-item" data-tab-index="' + index + '"><a href="#' + newTabId + '">Address ' + index + '</a></li>');

            // add the new address to the content list
            this.element.find('#address_form_container').append('<div id="' + newTabId + '" class="address-item-edit-content" data-tab-index="' + index + '">Address ' + index + '<p>Name: <input type="text" name="name.' + index + '"/></p></div>');

            // refresh the widget to pick up the newly added tab.
            this.refresh();

            // active the newly added tab
            this.option('active', index - 1);
        },

        /**
         * This method is used to bind events associated with this widget.
         */
        _bind: function() {
            this._on(this.element.find(':button[data-ui-id="customer-edit-tab-addresses-add-address-button"]'),
                {'click': '_addNewAddress'});
        },

        _create: function() {
            this._super();
            this._bind();
        },

        /**
         * This method returns the maximum data index currently on the page.
         */
        _getMaxIndex: function() {
            var index = 0;

            this.element.find('div[data-tab-index]').each(function() {
                // convert the index found in the attribute to a numerical value -- ? error on non-number?
                var currentIndex = Number($(this).attr('data-tab-index'));
                if (currentIndex > index) {
                    index = currentIndex;
                }
            });

            return index;
        },

        _prepareTemplate : function(template){
            // @TODO Replace '_template_' with data-mage-init option <?php echo $_templatePrefix ?>
            return template
                .replace(/_template_/g, '_item' + this.options.itemCount)
                .replace(/_counted="undefined"/g, '')
                .replace(/"select_button_"/g, 'select_button_' + this.options.itemCount)
                ;
        }
    });

    $.widget('mage.addressInputs', {
        options: {
        },

        /**
         * This method is used to bind events associated with this widget.
         */
        _bind: function() {
            this._on(this.element.find(':input'), {'change': '_triggerChange'});
        },

        _create: function() {
            this._super();
            this.bind();
        },

        /**
         * This method is used to trigger a change element for a given entity.
         */
        _triggerChange: function() {
            alert('Something changed...');
        }
    });
})(jQuery);