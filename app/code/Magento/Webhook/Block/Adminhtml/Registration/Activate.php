<?php
/**
 * Creates block with an activation template
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Block\Adminhtml\Registration;

class Activate extends \Magento\Backend\Block\Template
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
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
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
