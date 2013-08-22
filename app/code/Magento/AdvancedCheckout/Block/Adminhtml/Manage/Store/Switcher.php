<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Store switcher for shopping cart management
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_AdvancedCheckout_Block_Adminhtml_Manage_Store_Switcher extends Magento_Backend_Block_Store_Switcher
{
    /**
     * @var bool
     */
    protected $_hasDefaultOption = false;

    /**
     * Add website filter
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setUseConfirm(false);
        if ($this->_getCustomer() && $this->_getCustomer()->getSharingConfig()->isWebsiteScope()) {
            $this->setWebsiteIds($this->_getCustomer()->getSharedWebsiteIds());
        }
    }

    /**
     * Return current customer from regisrty
     *
     * @return Magento_Customer_Model_Customer
     */
    protected function _getCustomer()
    {
        return Mage::registry('checkout_current_customer');
    }

    /**
     * Return current store from regisrty
     *
     * @return Magento_Core_Model_Store
     */
    protected function _getStore()
    {
        return Mage::registry('checkout_current_store');
    }
}
