<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Rss_Block_Abstract extends Magento_Core_Block_Template
{
    /**
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Customer_Model_Session $customerSession
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Customer_Model_Session $customerSession,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Get store id
     *
     * @return int
     */
    protected function _getStoreId()
    {
        $storeId =   (int) $this->getRequest()->getParam('store_id');
        if($storeId == null) {
           $storeId = $this->_storeManager->getStore()->getId();
        }
        return $storeId;
    }

    /**
     * Get customer group id
     *
     * @return int
     */
    protected function _getCustomerGroupId()
    {
        $customerGroupId =   (int) $this->getRequest()->getParam('cid');
        if($customerGroupId == null) {
            $customerGroupId = $this->_customerSession->getCustomerGroupId();
        }
        return $customerGroupId;
    }
}
