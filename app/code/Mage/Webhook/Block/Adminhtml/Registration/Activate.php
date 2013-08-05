<?php
/**
 * Creates block with an activation template
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Block_Adminhtml_Registration_Activate extends Mage_Backend_Block_Template
{
    const DATA_NAME = 'name';
    const DATA_TOPICS = 'topics';
    /** Subscription Data key for getting the subscription id */
    const DATA_SUBSCRIPTION_ID = 'subscription_id';

    /** Registry key for getting subscription data */
    const REGISTRY_KEY_CURRENT_SUBSCRIPTION = 'current_subscription';

    /** @var array  */
    protected $_subscriptionData;

    /**
     * @param Mage_Backend_Block_Template_Context $context
     * @param Mage_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        Mage_Core_Model_Registry $registry,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_subscriptionData = $registry->registry(self::REGISTRY_KEY_CURRENT_SUBSCRIPTION);
    }

    /**
     * Gets accept url
     *
     * @return string
     */
    public function getAcceptUrl()
    {
        return $this->getUrl('*/*/accept', array('id' => $this->_subscriptionData[self::DATA_SUBSCRIPTION_ID]));
    }

    /**
     * Get subscription name
     *
     * @return string
     */
    public function getSubscriptionName()
    {
        return $this->_subscriptionData[self::DATA_NAME];
    }

    /**
     * Get list of topics for subscription
     *
     * @return string[]
     */
    public function getSubscriptionTopics()
    {
        return $this->_subscriptionData[self::DATA_TOPICS];
    }
}
