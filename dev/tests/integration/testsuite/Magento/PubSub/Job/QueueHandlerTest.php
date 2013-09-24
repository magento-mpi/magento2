<?php
/**
 * \Magento\PubSub\Job\QueueHandler
 *
 * @magentoDbIsolation enabled
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PubSub\Job;

class QueueHandlerTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \Magento\ObjectManager */
    private $_objectManager;

    /** @var  \PHPUnit_Framework_MockObject_MockObject  */
    private $_transportMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $_responseMock;

    /** @var  \Magento\Webhook\Model\Event */
    private $_event;

    /** @var  \Magento\Webhook\Model\Subscription */
    private $_subscription;
    
    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        // Must mock transport to avoid actual network actions
        $this->_transportMock = $this->getMockBuilder('Magento\Outbound\Transport\Http')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_responseMock = $this->getMockBuilder('Magento\Outbound\Transport\Http\Response')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_transportMock->expects($this->any())
            ->method('dispatch')
            ->will($this->returnValue($this->_responseMock));

        /** \Magento\Webapi\Model\Acl\User $user */
        $user = $this->_objectManager->create('Magento\Webapi\Model\Acl\User')
            ->setSecret('shhh...')
            ->setApiKey(uniqid())
            ->save();

        /** @var \Magento\Webhook\Model\Event $_event */
        $this->_event = \Mage::getModel('Magento\Webhook\Model\Event')
            ->setTopic('topic')
            ->setBodyData(array('body data'))
            ->save();
        /** @var \Magento\Webhook\Model\Subscription $_subscription */
        $this->_subscription = $this->_objectManager->create('Magento\Webhook\Model\Subscription')
            ->setFormat('json')
            ->setAuthenticationType('hmac')
            ->setApiUserId($user->getId())
            ->save();
    }
    
    /**
     * Test the main flow of event queue handling given a successful job
     */
    public function testHandleSuccess()
    {
        $this->_responseMock->expects($this->any())
            ->method('isSuccessful')
            ->will($this->returnValue(true));

        $queueWriter = $this->_objectManager->create('Magento\PubSub\Job\QueueWriterInterface');

        /** @var \Magento\Webhook\Model\Job $job */
        $job = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Webhook\Model\Job');
        $job->setEventId($this->_event->getId());
        $job->setSubscriptionId($this->_subscription->getId());
        $jobId = $job->save()
            ->getId();
        $queueWriter->offer($job);

        // Must clear collection to avoid interaction with other tests
        /** @var \Magento\Webhook\Model\Resource\Job\Collection $collection */
        $collection = $this->_objectManager->create('Magento\Webhook\Model\Resource\Job\Collection');
        $collection->removeAllItems();
        $queueArgs = array(
            'collection' => $collection
        );

        $queueReader = $this->_objectManager->create('Magento\PubSub\Job\QueueReaderInterface', $queueArgs);
        $queueHandlerArgs = array(
            'jobQueueReader' => $queueReader,
            'jobQueueWriter' => $queueWriter,
            'transport' => $this->_transportMock
        );

        /** @var \Magento\PubSub\Job\QueueHandler $queueHandler */
        $queueHandler = $this->_objectManager->create('Magento\PubSub\Job\QueueHandler', $queueHandlerArgs);
        $queueHandler->handle();
        $loadedJob = $this->_objectManager->create('Magento\Webhook\Model\Job')
            ->load($jobId);

        $this->assertEquals(\Magento\PubSub\JobInterface::STATUS_SUCCEEDED, $loadedJob->getStatus());
    }

    public function testHandleFailure()
    {
        $this->_responseMock->expects($this->any())
            ->method('isSuccessful')
            ->will($this->returnValue(false));

        $queueWriter = $this->_objectManager->create('Magento\PubSub\Job\QueueWriterInterface');

        /** @var \Magento\Webhook\Model\Job $job */
        $job = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Webhook\Model\Job');
        $job->setEventId($this->_event->getId());
        $job->setSubscriptionId($this->_subscription->getId());
        $jobId = $job->save()
            ->getId();
        $queueWriter->offer($job);

        // Must clear collection to avoid interaction with other tests
        /** @var \Magento\Webhook\Model\Resource\Job\Collection $collection */
        $collection = $this->_objectManager->create('Magento\Webhook\Model\Resource\Job\Collection');
        $collection->removeAllItems();
        $queueArgs = array(
            'collection' => $collection
        );

        $queueReader = $this->_objectManager->create('Magento\PubSub\Job\QueueReaderInterface', $queueArgs);
        $queueHandlerArgs = array(
            'jobQueueReader' => $queueReader,
            'jobQueueWriter' => $queueWriter,
            'transport' => $this->_transportMock
        );

        /** @var \Magento\PubSub\Job\QueueHandler $queueHandler */
        $queueHandler = $this->_objectManager->create('Magento\PubSub\Job\QueueHandler', $queueHandlerArgs);
        $queueHandler->handle();
        $loadedJob = $this->_objectManager->create('Magento\Webhook\Model\Job')
            ->load($jobId);

        $this->assertEquals(\Magento\PubSub\JobInterface::STATUS_RETRY, $loadedJob->getStatus());
    }
}
