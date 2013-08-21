<?php
/**
 * Handles HTTP responses, and manages retry schedule
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @method bool hasEvent()
 * @method Magento_Webhook_Model_Job setEventId()
 * @method int getEventId()
 * @method bool hasSubscription()
 * @method Magento_Webhook_Model_Job setSubscriptionId()
 * @method int getSubscriptionId()
 * @method int getRetryCount()
 * @method Magento_Webhook_Model_Job setRetryCount()
 * @method Magento_Webhook_Model_Job setRetryAt()
 * @method Magento_Webhook_Model_Job setUpdatedAt()
 * @method Magento_Webhook_Model_Job setCreatedAt()
 */
class Magento_Webhook_Model_Job extends Magento_Core_Model_Abstract implements Magento_PubSub_JobInterface
{
    /** @var  Magento_Webhook_Model_Event_Factory */
    protected $_eventFactory;

    /** @var Magento_Webhook_Model_Subscription_Factory */
    protected $_subscriptionFactory;

    /** @var array */
    private $_retryTimeToAdd = array(
        1 => 1,
        2 => 2,
        3 => 4,
        4 => 10,
        5 => 30,
        6 => 60,
        7 => 120,
        8 => 240,
    );

    /**
     * @param Magento_Webhook_Model_Event_Factory $eventFactory
     * @param Magento_Webhook_Model_Subscription_Factory $subscriptionFactory
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Webhook_Model_Event_Factory $eventFactory,
        Magento_Webhook_Model_Subscription_Factory $subscriptionFactory,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_eventFactory = $eventFactory;
        $this->_subscriptionFactory = $subscriptionFactory;
        parent::__construct($context, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize model
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('Magento_Webhook_Model_Resource_Job');

        if ($this->hasEvent()) {
            $this->setEventId($this->getEvent()->getId());
        }

        if ($this->hasSubscription()) {
            $this->setSubscriptionId($this->getSubscription()->getId());
        }
        $this->setStatus(Magento_PubSub_JobInterface::STATUS_READY_TO_SEND);
    }

    /**
     * Prepare data to be saved to database
     *
     * @return Magento_Webhook_Model_Job
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if ($this->isObjectNew()) {
            $this->setCreatedAt($this->_getResource()->formatDate(true));
        } elseif ($this->getId() && !$this->hasData('updated_at')) {
            $this->setUpdatedAt($this->_getResource()->formatDate(true));
        }
        return $this;
    }

    /**
     * Get event
     *
     * @return Magento_PubSub_EventInterface|Magento_Webhook_Model_Event|null
     */
    public function getEvent()
    {
        if ($this->hasData('event')) {
            return $this->getData('event');
        }

        if ($this->hasData('event_id')) {
            $event = $this->_eventFactory->createEmpty()
                ->load($this->getEventId());
            $this->setData('event', $event);
            return $event;
        }

        return null;
    }

    /**
     * Get subscription
     *
     * @return Magento_Webhook_Model_Subscription|null
     */
    public function getSubscription()
    {
        if ($this->hasData('subscription')) {
            return $this->getData('subscription');
        }

        if ($this->hasData('subscription_id')) {
            $subscription = $this->_subscriptionFactory->create()
                ->load($this->getSubscriptionId());

            $this->setData('subscription', $subscription);
            return $subscription;
        }

        return null;
    }

    /**
     * Update the Job status to indicate it has completed successfully
     *
     * @return Magento_Webhook_Model_Job
     */
    public function complete()
    {
        $this->setStatus(Magento_PubSub_JobInterface::STATUS_SUCCEEDED)
            ->save();
        return $this;
    }

    /**
     * Handles failed HTTP response
     *
     * @return Magento_Webhook_Model_Job
     */
    public function handleFailure()
    {
        $retryCount = $this->getRetryCount();
        if ($retryCount < count($this->_retryTimeToAdd)) {
            $addedTimeInMinutes = $this->_retryTimeToAdd[$retryCount + 1] * 60 + time();
            $this->setRetryCount($retryCount + 1);
            $this->setRetryAt(Magento_Date::formatDate($addedTimeInMinutes));
            $this->setUpdatedAt(Magento_Date::formatDate(time(), true));
            $this->setStatus(Magento_PubSub_JobInterface::STATUS_RETRY);
        } else {
            $this->setStatus(Magento_PubSub_JobInterface::STATUS_FAILED);
        }
        return $this;
    }

    /**
     * Retrieve the status of the Job
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->getData('status');
    }

    /**
     * Set the status of the Job
     *
     * @param int $status
     * @return Magento_Webhook_Model_Job
     */
    public function setStatus($status)
    {
        $this->setData('status', $status);
        return $this;
    }
}
