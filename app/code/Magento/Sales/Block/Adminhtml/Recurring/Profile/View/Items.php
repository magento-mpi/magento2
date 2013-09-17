<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml recurring profile items grid
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Block_Adminhtml_Recurring_Profile_View_Items extends Magento_Adminhtml_Block_Sales_Items_Abstract
{
    /**
     * Retrieve required options from parent
     */
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            Mage::throwException(__('Invalid parent block for this block'));
        }
        parent::_beforeToHtml();
    }

    /**
     * Return current recurring profile
     *
     * @return Magento_Sales_Model_Recurring_Profile
     */
    public function _getRecurringProfile()
    {
        return $this->_coreRegistry->registry('current_recurring_profile');
    }

    /**
     * Retrieve recurring profile item
     *
     * @return Magento_Sales_Model_Order_Item
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

