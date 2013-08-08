<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Dashboard Customer Info
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Customer_Block_Account_Dashboard_Info extends Magento_Core_Block_Template
{
    /**
     * Cached subscription object
     * @var Mage_Newsletter_Model_Subscriber
     */
    protected $_subscription;

    public function getCustomer()
    {
        return Mage::getSingleton('Mage_Customer_Model_Session')->getCustomer();
    }

    public function getChangePasswordUrl()
    {
        return Mage::getUrl('*/account/edit/changepass/1');
    }

    /**
     * Get Customer Subscription Object Information
     *
     * @return Mage_Newsletter_Model_Subscriber
     */
    public function getSubscriptionObject()
    {
        if (!$this->_subscription) {
            $this->_subscription = Mage::getModel('Mage_Newsletter_Model_Subscriber');
            $this->_subscription->loadByCustomer(Mage::getSingleton('Mage_Customer_Model_Session')->getCustomer());
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
        return $this->getLayout()->getBlockSingleton('Mage_Customer_Block_Form_Register')->isNewsletterEnabled();
    }
}
