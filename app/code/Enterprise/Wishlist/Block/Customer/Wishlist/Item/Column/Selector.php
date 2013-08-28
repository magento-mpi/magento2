<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wishlist item selector in wishlist table
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Wishlist_Block_Customer_Wishlist_Item_Column_Selector
    extends Magento_Wishlist_Block_Customer_Wishlist_Item_Column
{
    /**
     * Render block
     *
     * @return bool
     */
    public function isEnabled()
    {
        return Mage::helper('Enterprise_Wishlist_Helper_Data')->isMultipleEnabled() || $this->getIsEnabled();
    }

    /**
     * Retrieve column title
     *
     * @return string
     */
    public function getTitle()
    {
        return '<input type="checkbox" id="select-all" />';
    }

    /**
     * Get block javascript
     *
     * @return string
     */
    public function getJs()
    {
        return parent::getJs() . "
            var selector = $('select-all'),
                checkboxes = $(selector).up('#wishlist-table').select('.select'),
                counter = 0;
            if (!checkboxes.length) {
                selector.hide();
            }
            selector.setCounter = function (newVal) {
                counter = newVal;
                this.checked = (counter >= checkboxes.length);
            }
            selector.onclick = function(){
                checkboxes.each( (function(checkbox) {
                    checkbox.checked = this.checked;
                }).bind(this));
                counter = this.checked ? checkboxes.length : 0
            };
            checkboxes.each( function(checkbox) {
                checkbox.onclick = function() {
                    selector.setCounter(this.checked ? counter + 1: counter -1);
                }
            });
        ";
    }
}
