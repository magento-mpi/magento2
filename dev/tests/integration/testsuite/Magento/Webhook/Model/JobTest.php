<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model;

/**
 * \Magento\Webhook\Model\Job
 *
 * @magentoDbIsolation enabled
 */
class JobTest extends \PHPUnit_Framework_TestCase
{
    /**
     * mock endpoint url
     */
    const ENDPOINT_URL = 'http://localhost/';
    const SUCCESS_RESPONSE = 200;
    const FAILURE_RESPONSE = 404;

    /**
     * @var \Magento\Webhook\Model\Job
     */
    protected $_job;

    public function setUp()
    {
        $this->_job = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Webhook\Model\Job');
    }

    public function testConstruct()
    {
        $this->markTestSkipped("MAGETWO-11929 uncaught exception");
        $event = \Mage::getModel('Magento\Webhook\Model\Event')
            ->setDataChanges(true)
            ->save();
        $eventId = $event->getId();
        $subscription = \Mage::getModel('Magento\Webhook\Model\Subscription')
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
        $this->markTestSkipped("MAGETWO-11929 uncaught exception");
        $eventId = \Mage::getModel('Magento\Webhook\Model\Event')
            ->setDataChanges(true)
            ->save()
            ->getId();
        $this->_job->setData('event_id', $eventId);
        $this->assertEquals($eventId, $this->_job->getEvent()->getId());
    }

    public function testGetEvent()
    {
        $this->markTestSkipped("MAGETWO-11929 uncaught exception");
        $event = \Mage::getModel('Magento\Webhook\Model\Event')
            ->setDataChanges(true)
            ->save();
        $this->_job->setData('event', $event);
        $this->assertEquals($event, $this->_job->getEvent());
    }

    public function testGetSubscriptionById()
    {
        $this->markTestSkipped("MAGETWO-11929 uncaught exception");
        $subscriptionId = \Mage::getModel('Magento\Webhook\Model\Subscription')
            ->setDataChanges(true)
            ->save()
            ->getId();
        $this->_job->setData('subscription_id', $subscriptionId);
        $this->assertEquals($subscriptionId, $this->_job->getSubscription()->getId());
    }

    public function testGetSubscription()
    {
        $this->markTestSkipped("MAGETWO-11929 uncaught exception");
        $subscription = \Mage::getModel('Magento\Webhook\Model\Subscription')
            ->setDataChanges(true)
            ->save();
        $this->_job->setData('subscription', $subscription);
        $this->assertEquals($subscription, $this->_job->getSubscription());
    }

    public function testGetNonexistent()
    {
        $this->markTestSkipped("MAGETWO-11929 uncaught exception");
        $this->assertEquals(null, $this->_job->getEvent());
        $this->assertEquals(null, $this->_job->getSubscription());
    }

    public function testHandleResponseSuccess()
    {
        $this->markTestSkipped("MAGETWO-11929 uncaught exception");
        $subscriptionId = \Mage::getModel('Magento\Webhook\Model\Subscription')
            ->setDataChanges(true)
            ->save()
            ->getId();
        $eventId = \Mage::getModel('Magento\Webhook\Model\Event')
            ->setDataChanges(true)
            ->save()
            ->getId();
        $this->_job->setData('subscription_id', $subscriptionId);
        $this->_job->setData('event_id', $eventId);

        $this->_job->complete();
        $this->assertEquals(\Magento\PubSub\JobInterface::STATUS_SUCCEEDED, $this->_job->getStatus());
    }

    public function testHandleResponseRetry()
    {
        $this->markTestSkipped("MAGETWO-11929 uncaught exception");
        $subscriptionId = \Mage::getModel('Magento\Webhook\Model\Subscription')
            ->setDataChanges(true)
            ->save()
            ->getId();
        $this->_job->setData('subscription_id', $subscriptionId);

        $eventId = \Mage::getModel('Magento\Webhook\Model\Event')
            ->setDataChanges(true)
            ->save()
            ->getId();
        $this->_job->setData('event_id', $eventId);

        $this->_job->handleFailure();
        $this->assertEquals(\Magento\PubSub\JobInterface::STATUS_RETRY, $this->_job->getStatus());
    }

    public function testHandleFailure()
    {
        $this->markTestSkipped("MAGETWO-11929 uncaught exception");
        $count = 1;
        while ($count <= 8) {
            $this->_job->handleFailure();
            $this->assertEquals(\Magento\PubSub\JobInterface::STATUS_RETRY, $this->_job->getStatus());
            $this->assertEquals($count, $this->_job->getRetryCount());
            $count++;
        }
        $this->_job->handleFailure();
        $this->assertEquals(\Magento\PubSub\JobInterface::STATUS_FAILED, $this->_job->getStatus());
    }
}
