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
namespace Magento\AdvancedCheckout\Block\Adminhtml\Manage\Store;

class Switcher extends \Magento\Backend\Block\Store\Switcher
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
     * @return \Magento\Customer\Model\Customer
     */
    protected function _getCustomer()
    {
        return \Mage::registry('checkout_current_customer');
    }

    /**
     * Return current store from regisrty
     *
     * @return \Magento\Core\Model\Store
     */
    protected function _getStore()
    {
        return \Mage::registry('checkout_current_store');
    }
}
