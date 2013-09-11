<?php
/**
 * Factory for \Magento\Webhook\Model\Job
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model\Job;

class Factory implements \Magento\PubSub\Job\FactoryInterface
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Initialize the class
     *
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create Job
     *
     * @param \Magento\PubSub\SubscriptionInterface $subscription
     * @param \Magento\PubSub\EventInterface $event
     * @return \Magento\PubSub\JobInterface
     */
    public function create(\Magento\PubSub\SubscriptionInterface $subscription, \Magento\PubSub\EventInterface $event)
    {
        return $this->_objectManager->create('Magento\Webhook\Model\Job', array(
            'data' => array(
                'event' => $event,
                'subscription' => $subscription
            )
        ));
    }
}
