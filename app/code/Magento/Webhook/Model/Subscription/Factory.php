<?php
/**
 * Factory for \Magento\Webhook\Model\Subscription
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model\Subscription;

class Factory
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
     * Create a new instance of \Magento\Webhook\Model\Subscription
     *
     * @param array $data Data for our subscription
     * @return \Magento\Webhook\Model\Subscription
     */
    public function create(array $data = array())
    {
        $subscription = $this->_objectManager->create('Magento\Webhook\Model\Subscription', array());
        // Don't set data in the constructor as it bypasses our special case logic in setData function.
        $subscription->setData($data);
        return $subscription;
    }
}
