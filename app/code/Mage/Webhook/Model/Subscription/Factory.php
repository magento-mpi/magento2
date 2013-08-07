<?php
/**
 * Factory for Mage_Webhook_Model_Subscription
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Subscription_Factory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create a new instance of Mage_Webhook_Model_Subscription
     *
     * @param array $data Data for our subscription
     * @return Mage_Webhook_Model_Subscription
     */
    public function create(array $data = array())
    {
        $subscription = $this->_objectManager->create('Mage_Webhook_Model_Subscription', array());
        // Don't set data in the constructor as it bypasses our special case logic in setData function.
        $subscription->setData($data);
        return $subscription;
    }
}