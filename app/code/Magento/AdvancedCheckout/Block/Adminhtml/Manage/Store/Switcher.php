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
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_App $application
     * @param Magento_Core_Model_Website_Factory $websiteFactory
     * @param Magento_Core_Model_Store_Group_Factory $storeGroupFactory
     * @param Magento_Core_Model_StoreFactory $storeFactory
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_App $application,
        Magento_Core_Model_Website_Factory $websiteFactory,
        Magento_Core_Model_Store_Group_Factory $storeGroupFactory,
        Magento_Core_Model_StoreFactory $storeFactory,
        Magento_Core_Model_Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct(
            $coreData, $context, $application, $websiteFactory, $storeGroupFactory, $storeFactory, $data
        );
    }

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
     * Return current customer from registry
     *
     * @return \Magento\Customer\Model\Customer
     */
    protected function _getCustomer()
    {
        return $this->_coreRegistry->registry('checkout_current_customer');
    }

    /**
     * Return current store from registry
     *
     * @return \Magento\Core\Model\Store
     */
    protected function _getStore()
    {
        return $this->_coreRegistry->registry('checkout_current_store');
    }
}
