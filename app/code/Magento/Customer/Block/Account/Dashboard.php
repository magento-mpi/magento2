<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer dashboard block
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Customer_Block_Account_Dashboard extends Magento_Core_Block_Template
{
    protected $_subscription = null;

    public function getCustomer()
    {
        return Mage::getSingleton('Magento_Customer_Model_Session')->getCustomer();
    }

    public function getAccountUrl()
    {
        return Mage::getUrl('customer/account/edit', array('_secure'=>true));
    }

    public function getAddressesUrl()
    {
        return Mage::getUrl('customer/address/index', array('_secure'=>true));
    }

    public function getAddressEditUrl($address)
    {
        return Mage::getUrl('customer/address/edit', array('_secure'=>true, 'id'=>$address->getId()));
    }

    public function getOrdersUrl()
    {
        return Mage::getUrl('customer/order/index', array('_secure'=>true));
    }

    public function getReviewsUrl()
    {
        return Mage::getUrl('review/customer/index', array('_secure'=>true));
    }

    public function getWishlistUrl()
    {
        return Mage::getUrl('customer/wishlist/index', array('_secure'=>true));
    }

    public function getTagsUrl()
    {

    }

    public function getSubscriptionObject()
    {
        if(is_null($this->_subscription)) {
            $this->_subscription = Mage::getModel('Magento_Newsletter_Model_Subscriber')->loadByCustomer($this->getCustomer());
        }

        return $this->_subscription;
    }

    public function getManageNewsletterUrl()
    {
        return $this->getUrl('*/newsletter/manage');
    }

    public function getSubscriptionText()
    {
        if($this->getSubscriptionObject()->isSubscribed()) {
            return Mage::helper('Magento_Customer_Helper_Data')->__('You subscribe to our newsletter.');
        }

        return Mage::helper('Magento_Customer_Helper_Data')->__('You are currently not subscribed to our newsletter.');
    }

    public function getPrimaryAddresses()
    {
        $addresses = $this->getCustomer()->getPrimaryAddresses();
        if (empty($addresses)) {
            return false;
        }
        return $addresses;
    }

    /**
     * Get back url in account dashboard
     *
     * This method is copypasted in:
     * Magento_Wishlist_Block_Customer_Wishlist  - because of strange inheritance
     * Magento_Customer_Block_Address_Book - because of secure url
     *
     * @return string
     */
    public function getBackUrl()
    {
        // the RefererUrl must be set in appropriate controller
        if ($this->getRefererUrl()) {
            return $this->getRefererUrl();
        }
        return $this->getUrl('customer/account/');
    }
}
