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
            itemCount: 0,
            baseItemId: 'new_item',
            // @TODO obtain default countries
            defaultCountries: null
        },

        _create: function() {
            this._super();
            this._bind();
            this.options.itemCount = $(".address-list .address-list-item").length;
        },

        _bind: function() {
            $('[data-ui-id="customer-edit-tab-addresses-add-address-button"]').click($.proxy(this.addNewAddress, this));
        },

        addNewAddress : function(event){
            this.options.itemCount++;

            // preventing duplication of ids
            while ($("form_address_item_" + this.options.itemCount).length) {
                this.options.itemCount++;
            }

            $('.address-item-edit').append('<div id="' + 'form_' + this.options.baseItemId + this.options.itemCount + '" class="address-item-edit-content">'
                + this.prepareTemplate($('#address_form_template').html())
                + '</div>');

            var newForm = $('#form_' + this.options.baseItemId + this.options.itemCount);

            $('#_item' + this.options.itemCount + 'firstname').val($('#_accountfirstname').val());
            $('#_item' + this.options.itemCount + 'lastname').val($('#_accountlastname').val());

            // @TODO something different?
            var template = this.prepareTemplate($('#address_item_template').html())
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

        prepareTemplate : function(template){
            // @TODO Replace '_template_' with data-mage-init option <?php echo $_templatePrefix ?>
            return template
                .replace(/_template_/g, '_item' + this.options.itemCount)
                .replace(/_counted="undefined"/g, '')
                .replace(/"select_button_"/g, 'select_button_' + this.options.itemCount)
                ;
        }
   });


    $(document).ready(function() {
        $("#address-tabs").mage('addressTabs');
    });
})(jQuery);