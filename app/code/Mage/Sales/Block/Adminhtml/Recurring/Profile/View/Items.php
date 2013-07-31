<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml recurring profile items grid
 *
 * @category   Mage
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Block_Adminhtml_Recurring_Profile_View_Items extends Magento_Adminhtml_Block_Sales_Items_Abstract
{
    /**
     * Retrieve required options from parent
     */
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            Mage::throwException(Mage::helper('Magento_Adminhtml_Helper_Data')->__('Invalid parent block for this block'));
        }
        parent::_beforeToHtml();
    }

    /**
     * Return current recurring profile
     *
     * @return Mage_Sales_Model_Recurring_Profile
     */
    public function _getRecurringProfile()
    {
        return Mage::registry('current_recurring_profile');
    }

    /**
     * Retrieve recurring profile item
     *
     * @return Mage_Sales_Model_Order_Item
     */
    public function getItem()
    {
        return $this->_getRecurringProfile()->getItem();
    }

    /**
     * Retrieve formated price
     *
     * @param   decimal $value
     * @return  string
     */
    public function formatPrice($value)
    {
        $store = Mage::app()->getStore($this->_getRecurringProfile()->getStore());
        return $store->formatPrice($value);
    }
}

