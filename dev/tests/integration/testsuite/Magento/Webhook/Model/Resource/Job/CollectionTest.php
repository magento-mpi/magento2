<?php
/**
 * Magento_Webhook_Model_Resource_Job_Collection
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Resource_Job_CollectionTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Webhook_Model_Subscription */
    protected $_subscription;

    /** @var Magento_Webhook_Model_Event */
    protected $_event;

    /** @var Magento_Webhook_Model_Endpoint */
    protected $_endpoint;

    /** @var Magento_Webapi_Model_Acl_User */
    protected $_user;

    public function setUp()
    {
        $this->_user = Mage::getObjectManager()->create('Magento_Webapi_Model_Acl_User')
            ->setApiKey(md5(rand(0, time())))
            ->save();
        $this->_endpoint = Mage::getObjectManager()->create('Magento_Webhook_Model_Endpoint')
            ->setEndpointUrl('test')
            ->setTimeoutInSecs('test')
            ->setFormat('test')
            ->setAuthenticationType('authentication_type');
        $this->_subscription = Mage::getObjectManager()->create('Magento_Webhook_Model_Subscription',
            array('endpoint' => $this->_endpoint))
            ->setApiUserId($this->_user->getId())
            ->save();
        $this->_event = Mage::getObjectManager()->create('Magento_Webhook_Model_Event')
            ->save();
    }

    public function tearDown()
    {
        $this->_subscription->delete();
        $this->_event->delete();
        $this->_endpoint->delete();
        $this->_user->delete();
    }

    public function testInit()
    {
        /** @var Magento_Webhook_Model_Resource_Job_Collection $collection */
        $collection = Mage::getObjectManager()->create('Magento_Webhook_Model_Resource_Job_Collection');
        $this->assertEquals('Magento_Webhook_Model_Resource_Job', $collection->getResourceModelName());
        $this->assertEquals('Magento_Webhook_Model_Job', $collection->getModelName());
    }

    public function testNewEventInNewCollection()
    {
        $job1 = Mage::getObjectManager()->create('Magento_Webhook_Model_Job')
            ->setSubscriptionId($this->_subscription->getId())
            ->setEventId($this->_event->getId())
            ->save();

        /** @var Magento_Webhook_Model_Resource_Job_Collection $collection */
        $collection = Mage::getObjectManager()->create('Magento_Webhook_Model_Resource_Job_Collection');
        $this->assertEquals(1, count($collection->getItems()));
        $this->assertEquals($job1->getId(), $collection->getFirstItem()->getId());

        $job2 = Mage::getObjectManager()->create('Magento_Webhook_Model_Job')
            ->setSubscriptionId($this->_subscription->getId())
            ->setEventId($this->_event->getId())
            ->save();

        /** @var Magento_Webhook_Model_Resource_Job_Collection $collectionSecond */
        $collectionSecond = Mage::getObjectManager()->create('Magento_Webhook_Model_Resource_Job_Collection');
        $this->assertEquals(1, count($collectionSecond->getItems()));
        $this->assertEquals($job2->getId(), $collectionSecond->getFirstItem()->getId(),
            sprintf("Event #%s is expected in second collection,"
                . "found event #%s. It could lead to race conditions issue if it is #%s",
                $job2->getId(), $collectionSecond->getFirstItem()->getId(), $job1->getId())
        );

        $job1->delete();
        $job2->delete();
    }

    /**
     * Emulates concurrent transactions. Executes 50 seconds because of lock timeout
     *
     * @expectedException Zend_Db_Statement_Exception
     * @expectedMessage SQLSTATE[HY000]: General error: 1205 Lock wait timeout exceeded; try restarting transaction
     */
    public function testParallelTransactions()
    {
        $job = Mage::getObjectManager()->create('Magento_Webhook_Model_Job')
            ->setSubscriptionId($this->_subscription->getId())
            ->setEventId($this->_event->getId())
            ->save();
        $job2 = Mage::getObjectManager()->create('Magento_Webhook_Model_Job')
            ->setSubscriptionId($this->_subscription->getId())
            ->setEventId($this->_event->getId())
            ->save();
        $job3 = Mage::getObjectManager()->create('Magento_Webhook_Model_Job')
            ->setSubscriptionId($this->_subscription->getId())
            ->setEventId($this->_event->getId())
            ->setStatus(Magento_PubSub_JobInterface::STATUS_IN_PROGRESS)
            ->save();

        /** @var Magento_Webhook_Model_Resource_Job_Collection $collection */
        $collection = Mage::getObjectManager()->create('Magento_Webhook_Model_Resource_Job_Collection');

        $beforeLoad = new ReflectionMethod(
            'Magento_Webhook_Model_Resource_Job_Collection', '_beforeLoad');
        $beforeLoad->setAccessible(true);
        $beforeLoad->invoke($collection);
        $data = $collection->getData();
        $this->assertEquals(2, count($data));

        /** @var Magento_Core_Model_Resource $resource */
        $resource = Mage::getObjectManager()->create('Magento_Core_Model_Resource');
        $connection = $resource->getConnection('core_write');

        /** @var Magento_Webhook_Model_Resource_Job_Collection $collection2 */
        $collection2 = Mage::getObjectManager()->create('Magento_Webhook_Model_Resource_Job_Collection');
        $collection2->setConnection($connection);
        $initSelect = new ReflectionMethod(
            'Magento_Webhook_Model_Resource_Job_Collection', '_initSelect');
        $initSelect->setAccessible(true);
        $initSelect->invoke($collection2);


        $afterLoad = new ReflectionMethod(
            'Magento_Webhook_Model_Resource_Job_Collection', '_afterLoad');
        $afterLoad->setAccessible(true);


        try {
            $collection2->getData();
        } catch (Zend_Db_Statement_Exception $e) {
            $job->delete();
            $job2->delete();
            $job3->delete();
            $afterLoad->invoke($collection);

            throw ($e);
        }
        $job->delete();
        $job2->delete();
        $job3->delete();
        $afterLoad->invoke($collection);
    }

    public function testRevokeIdlingInProgress()
    {
        /** @var Magento_Webhook_Model_Resource_Event_Collection $collection */
        $collection = Mage::getObjectManager()->create('Magento_Webhook_Model_Resource_Event_Collection');
        $this->assertNull($collection->revokeIdlingInProgress());
    }
}
