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
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Customer_Model_Session $customerSession,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        parent::__construct($coreData, $context, $data);
    }

    protected function _getStoreId()
    {
        //store id is store view id
        $storeId =   (int) $this->getRequest()->getParam('store_id');
        if($storeId == null) {
           $storeId = Mage::app()->getStore()->getId();
        }
        return $storeId;
    }

    protected function _getCustomerGroupId()
    {
        //customer group id
        $custGroupID =   (int) $this->getRequest()->getParam('cid');
        if($custGroupID == null) {
            $custGroupID = $this->_customerSession->getCustomerGroupId();
        }
        return $custGroupID;
    }
}
