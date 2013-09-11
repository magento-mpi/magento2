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
namespace Magento\Catalog\Block\Product;

class Send extends \Magento\Catalog\Block\Product\AbstractProduct
{

    /**
     * Retrieve username for form field
     *
     * @return string
     */
    public function getUserName()
    {
        return \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomer()->getName();
    }

    public function getEmail()
    {
        return (string)\Mage::getSingleton('Magento\Customer\Model\Session')->getCustomer()->getEmail();
    }

    public function getProductId()
    {
        return $this->getRequest()->getParam('id');
    }

    public function getMaxRecipients()
    {
        $sendToFriendModel = \Mage::registry('send_to_friend_model');
        return $sendToFriendModel->getMaxRecipients();
    }
}
