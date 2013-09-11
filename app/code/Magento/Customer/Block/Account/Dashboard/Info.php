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

namespace Magento\Customer\Block\Account\Dashboard;

class Info extends \Magento\Core\Block\Template
{
    /**
     * Cached subscription object
     * @var \Magento\Newsletter\Model\Subscriber
     */
    protected $_subscription;

    public function getCustomer()
    {
        return \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomer();
    }

    public function getChangePasswordUrl()
    {
        return \Mage::getUrl('*/account/edit/changepass/1');
    }

    /**
     * Get Customer Subscription Object Information
     *
     * @return \Magento\Newsletter\Model\Subscriber
     */
    public function getSubscriptionObject()
    {
        if (!$this->_subscription) {
            $this->_subscription = \Mage::getModel('Magento\Newsletter\Model\Subscriber');
            $this->_subscription->loadByCustomer(\Mage::getSingleton('Magento\Customer\Model\Session')->getCustomer());
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
        return $this->getLayout()->getBlockSingleton('Magento\Customer\Block\Form\Register')->isNewsletterEnabled();
    }
}
