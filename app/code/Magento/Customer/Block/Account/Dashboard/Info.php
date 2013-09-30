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
     *
     * @var Magento_Newsletter_Model_Subscriber
     */
    protected $_subscription;

    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @var Magento_Newsletter_Model_SubscriberFactory
     */
    protected $_subscriberFactory;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Newsletter_Model_SubscriberFactory $subscriberFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Customer_Model_Session $customerSession,
        Magento_Newsletter_Model_SubscriberFactory $subscriberFactory,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        $this->_subscriberFactory = $subscriberFactory;
        parent::__construct($coreData, $context, $data);
    }


    public function getCustomer()
    {
        return $this->_customerSession->getCustomer();
    }

    public function getChangePasswordUrl()
    {
        return $this->_urlBuilder->getUrl('*/account/edit/changepass/1');
    }

    /**
     * Get Customer Subscription Object Information
     *
     * @return Magento_Newsletter_Model_Subscriber
     */
    public function getSubscriptionObject()
    {
        if (!$this->_subscription) {
            $this->_subscription = $this->_createSubscriber();
            $this->_subscription->loadByCustomer($this->_customerSession->getCustomer());
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

    /**
     * @return Magento_Newsletter_Model_Subscriber
     */
    protected function _createSubscriber()
    {
        return $this->_subscriberFactory->create();
    }
}
