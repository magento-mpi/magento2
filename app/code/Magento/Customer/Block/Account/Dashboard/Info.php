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
 * Dashboard Customer Info
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Customer_Block_Account_Dashboard_Info extends Magento_Core_Block_Template
{
    /**
     * Cached subscription object
     * @var Magento_Newsletter_Model_Subscriber
     */
    protected $_subscription;

    public function getCustomer()
    {
        return Mage::getSingleton('Magento_Customer_Model_Session')->getCustomer();
    }

    public function getChangePasswordUrl()
    {
        return Mage::getUrl('*/account/edit/changepass/1');
    }

    /**
     * Get Customer Subscription Object Information
     *
     * @return Magento_Newsletter_Model_Subscriber
     */
    public function getSubscriptionObject()
    {
        if (!$this->_subscription) {
            $this->_subscription = Mage::getModel('Magento_Newsletter_Model_Subscriber');
            $this->_subscription->loadByCustomer(Mage::getSingleton('Magento_Customer_Model_Session')->getCustomer());
        }
        return $this->_subscription;
    }

    /**
     * Gets Customer subscription status
     *
     * @return bool
     */
    public function getIsSubscribed()
    {
        return $this->getSubscriptionObject()->isSubscribed();
    }

    /**
     *  Newsletter module availability
     *
     *  @return	  boolean
     */
    public function isNewsletterEnabled()
    {
        return $this->getLayout()->getBlockSingleton('Magento_Customer_Block_Form_Register')->isNewsletterEnabled();
    }
}
