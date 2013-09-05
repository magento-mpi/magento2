<?php
/**
 * Factory for Magento_Webhook_Model_Subscription
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Subscription_Factory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create a new instance of Magento_Webhook_Model_Subscription
     *
     * @param array $data Data for our subscription
     * @return Magento_Webhook_Model_Subscription
     */
    public function create(array $data = array())
    {
        $subscription = $this->_objectManager->create('Magento_Webhook_Model_Subscription', array());
        // Don't set data in the constructor as it bypasses our special case logic in setData function.
        $subscription->setData($data);
        return $subscription;
    }
}