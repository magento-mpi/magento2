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
            tabLabel: 'tabs-'
        },

        /**
         * This method is used to add new address elements to the form.
         */
        _addNewAddress: function() {
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
            console.info("In the address tabs widget....");
        },

        /**
         * This method returns the maximum data index currently on the page.
         */
        _getMaxIndex: function() {
            var index = 0;

            this.element.find('div[data-tab-index]').each(function() {
                var currentIndex = Number($(this).attr('data-tab-index'));
                if (currentIndex > index) {
                    index = currentIndex;
                }
            });

            return index;
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