<?php
/**
 * Magento_PubSub_Job_QueueHandler
 *
 * @magentoDbIsolation enabled
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_PubSub_Job_QueueHandlerTest extends PHPUnit_Framework_TestCase
{
    /** @var  Magento_ObjectManager */
    private $_objectManager;

    /** @var  PHPUnit_Framework_MockObject_MockObject  */
    private $_transportMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_responseMock;

    /** @var  Magento_Webhook_Model_Event */
    private $_event;

    /** @var  Magento_Webhook_Model_Subscription */
    private $_subscription;
    
    public function setUp()
    {
        $this->_objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();

        // Must mock transport to avoid actual network actions
        $this->_transportMock = $this->getMockBuilder('Magento_Outbound_Transport_Http')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_responseMock = $this->getMockBuilder('Magento_Outbound_Transport_Http_Response')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_transportMock->expects($this->any())
            ->method('dispatch')
            ->will($this->returnValue($this->_responseMock));

        /** Magento_Webapi_Model_Acl_User $user */
        $user = $this->_objectManager->create('Magento_Webapi_Model_Acl_User')
            ->setSecret('shhh...')
            ->setApiKey(uniqid())
            ->save();

        /** @var Magento_Webhook_Model_Event $_event */
        $this->_event = Mage::getModel('Magento_Webhook_Model_Event')
            ->setTopic('topic')
            ->setBodyData(array('body data'))
            ->save();
        /** @var Magento_Webhook_Model_Subscription $_subscription */
        $this->_subscription = $this->_objectManager->create('Magento_Webhook_Model_Subscription')
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

        $queueWriter = $this->_objectManager->create('Magento_PubSub_Job_QueueWriterInterface');

        /** @var Magento_Webhook_Model_Job $job */
        $job = Magento_Test_Helper_Bootstrap::getObjectManager()->create('Magento_Webhook_Model_Job');
        $job->setEventId($this->_event->getId());
        $job->setSubscriptionId($this->_subscription->getId());
        $jobId = $job->save()
            ->getId();
        $queueWriter->offer($job);

        // Must clear collection to avoid interaction with other tests
        /** @var Magento_Webhook_Model_Resource_Job_Collection $collection */
        $collection = $this->_objectManager->create('Magento_Webhook_Model_Resource_Job_Collection');
        $collection->removeAllItems();
        $queueArgs = array(
            'collection' => $collection
        );

        $queueReader = $this->_objectManager->create('Magento_PubSub_Job_QueueReaderInterface', $queueArgs);
        $queueHandlerArgs = array(
            'jobQueueReader' => $queueReader,
            'jobQueueWriter' => $queueWriter,
            'transport' => $this->_transportMock
        );

        /** @var Magento_PubSub_Job_QueueHandler $queueHandler */
        $queueHandler = $this->_objectManager->create('Magento_PubSub_Job_QueueHandler', $queueHandlerArgs);
        $queueHandler->handle();
        $loadedJob = $this->_objectManager->create('Magento_Webhook_Model_Job')
            ->load($jobId);

        $this->assertEquals(Magento_PubSub_JobInterface::STATUS_SUCCEEDED, $loadedJob->getStatus());
    }

    public function testHandleFailure()
    {
        $this->_responseMock->expects($this->any())
            ->method('isSuccessful')
            ->will($this->returnValue(false));

        $queueWriter = $this->_objectManager->create('Magento_PubSub_Job_QueueWriterInterface');

        /** @var Magento_Webhook_Model_Job $job */
        $job = Magento_Test_Helper_Bootstrap::getObjectManager()->create('Magento_Webhook_Model_Job');
        $job->setEventId($this->_event->getId());
        $job->setSubscriptionId($this->_subscription->getId());
        $jobId = $job->save()
            ->getId();
        $queueWriter->offer($job);

        // Must clear collection to avoid interaction with other tests
        /** @var Magento_Webhook_Model_Resource_Job_Collection $collection */
        $collection = $this->_objectManager->create('Magento_Webhook_Model_Resource_Job_Collection');
        $collection->removeAllItems();
        $queueArgs = array(
            'collection' => $collection
        );

        $queueReader = $this->_objectManager->create('Magento_PubSub_Job_QueueReaderInterface', $queueArgs);
        $queueHandlerArgs = array(
            'jobQueueReader' => $queueReader,
            'jobQueueWriter' => $queueWriter,
            'transport' => $this->_transportMock
        );

        /** @var Magento_PubSub_Job_QueueHandler $queueHandler */
        $queueHandler = $this->_objectManager->create('Magento_PubSub_Job_QueueHandler', $queueHandlerArgs);
        $queueHandler->handle();
        $loadedJob = $this->_objectManager->create('Magento_Webhook_Model_Job')
            ->load($jobId);

        $this->assertEquals(Magento_PubSub_JobInterface::STATUS_RETRY, $loadedJob->getStatus());
    }
}
