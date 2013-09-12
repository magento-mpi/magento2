<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Product send to friend block
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @module     Catalog
 */
class Magento_Catalog_Block_Product_Send extends Magento_Catalog_Block_Product_Abstract
{
    /**
     * Retrieve username for form field
     *
     * @return string
     */
    public function getUserName()
    {
        return Mage::getSingleton('Magento_Customer_Model_Session')->getCustomer()->getName();
    }

    public function getEmail()
    {
        return (string)Mage::getSingleton('Magento_Customer_Model_Session')->getCustomer()->getEmail();
    }

    public function getProductId()
    {
        return $this->getRequest()->getParam('id');
    }

    public function getMaxRecipients()
    {
        $sendToFriendModel = $this->_coreRegistry->registry('send_to_friend_model');
        return $sendToFriendModel->getMaxRecipients();
    }
}
