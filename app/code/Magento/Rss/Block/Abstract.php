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
            $custGroupID = Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerGroupId();
        }
        return $custGroupID;
    }
}
