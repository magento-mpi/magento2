<?php
/**
 * Magento_Webhook_Model_Job
 *
 * @magentoDbIsolation enabled
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_JobTest extends PHPUnit_Framework_TestCase
{
    /**
     * mock endpoint url
     */
    const ENDPOINT_URL = 'http://localhost/';
    const SUCCESS_RESPONSE = 200;
    const FAILURE_RESPONSE = 404;

    /**
     * @var Magento_Webhook_Model_Job
     */
    protected $_job;

    public function setUp()
    {
        $this->_job = Mage::getObjectManager()->create('Magento_Webhook_Model_Job');
    }

    public function testConstruct()
    {
        $event = Mage::getModel('Magento_Webhook_Model_Event')
            ->setDataChanges(true)
            ->save();
        $eventId = $event->getId();
        $subscription = Mage::getModel('Magento_Webhook_Model_Subscription')
            ->setDataChanges(true)
            ->save();
        $subscriptionId = $subscription->getId();

        $this->_job->setEvent($event);
        $this->_job->setSubscription($subscription);
        $this->_job->_construct();

        $this->assertEquals($eventId, $this->_job->getEventId());
        $this->assertEquals($subscriptionId, $this->_job->getSubscriptionId());
    }

    public function testGetEventById()
    {
        $eventId = Mage::getModel('Magento_Webhook_Model_Event')
            ->setDataChanges(true)
            ->save()
            ->getId();
        $this->_job->setData('event_id', $eventId);
        $this->assertEquals($eventId, $this->_job->getEvent()->getId());
    }

    public function testGetEvent()
    {
        $event = Mage::getModel('Magento_Webhook_Model_Event')
            ->setDataChanges(true)
            ->save();
        $this->_job->setData('event', $event);
        $this->assertEquals($event, $this->_job->getEvent());
    }

    public function testGetSubscriptionById()
    {
        $subscriptionId = Mage::getModel('Magento_Webhook_Model_Subscription')
            ->setDataChanges(true)
            ->save()
            ->getId();
        $this->_job->setData('subscription_id', $subscriptionId);
        $this->assertEquals($subscriptionId, $this->_job->getSubscription()->getId());
    }

    public function testGetSubscription()
    {
        $subscription = Mage::getModel('Magento_Webhook_Model_Subscription')
            ->setDataChanges(true)
            ->save();
        $this->_job->setData('subscription', $subscription);
        $this->assertEquals($subscription, $this->_job->getSubscription());
    }

    public function testGetNonexistent()
    {
        $this->assertEquals(null, $this->_job->getEvent());
        $this->assertEquals(null, $this->_job->getSubscription());
    }

    public function testHandleResponseSuccess()
    {
        $subscriptionId = Mage::getModel('Magento_Webhook_Model_Subscription')
            ->setDataChanges(true)
            ->save()
            ->getId();
        $eventId = Mage::getModel('Magento_Webhook_Model_Event')
            ->setDataChanges(true)
            ->save()
            ->getId();
        $this->_job->setData('subscription_id', $subscriptionId);
        $this->_job->setData('event_id', $eventId);

        $this->_job->complete();
        $this->assertEquals(Magento_PubSub_JobInterface::STATUS_SUCCEEDED, $this->_job->getStatus());
    }

    public function testHandleResponseRetry()
    {
        $subscriptionId = Mage::getModel('Magento_Webhook_Model_Subscription')
            ->setDataChanges(true)
            ->save()
            ->getId();
        $this->_job->setData('subscription_id', $subscriptionId);

        $eventId = Mage::getModel('Magento_Webhook_Model_Event')
            ->setDataChanges(true)
            ->save()
            ->getId();
        $this->_job->setData('event_id', $eventId);

        $this->_job->handleFailure();
        $this->assertEquals(Magento_PubSub_JobInterface::STATUS_RETRY, $this->_job->getStatus());
    }

    public function testHandleFailure()
    {
        $count = 1;
        while ($count <= 8) {
            $this->_job->handleFailure();
            $this->assertEquals(Magento_PubSub_JobInterface::STATUS_RETRY, $this->_job->getStatus());
            $this->assertEquals($count, $this->_job->getRetryCount());
            $count++;
        }
        $this->_job->handleFailure();
        $this->assertEquals(Magento_PubSub_JobInterface::STATUS_FAILED, $this->_job->getStatus());
    }
}
