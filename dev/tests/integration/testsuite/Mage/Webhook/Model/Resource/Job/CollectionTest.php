<?php
/**
 * Mage_Webhook_Model_Resource_Job_Collection
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Resource_Job_CollectionTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webhook_Model_Subscription */
    protected $_subscription;

    /** @var Mage_Webhook_Model_Event */
    protected $_event;

    /** @var Mage_Webhook_Model_Endpoint */
    protected $_endpoint;

    /** @var Mage_Webapi_Model_Acl_User */
    protected $_user;

    public function setUp()
    {
        $this->_user = Mage::getObjectManager()->create('Mage_Webapi_Model_Acl_User')
            ->save();
        $this->_endpoint = Mage::getObjectManager()->create('Mage_Webhook_Model_Endpoint')
            ->setEndpointUrl('test')
            ->setTimeoutInSecs('test')
            ->setFormat('test')
            ->setAuthenticationType('authentication_type');
        $this->_subscription = Mage::getObjectManager()->create('Mage_Webhook_Model_Subscription',
            array('endpoint' => $this->_endpoint))
            ->setApiUserId($this->_user->getId())
            ->save();
        $this->_event = Mage::getObjectManager()->create('Mage_Webhook_Model_Event')
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
        /** @var Mage_Webhook_Model_Resource_Job_Collection $collection */
        $collection = Mage::getObjectManager()->create('Mage_Webhook_Model_Resource_Job_Collection');
        $this->assertEquals('Mage_Webhook_Model_Resource_Job', $collection->getResourceModelName());
        $this->assertEquals('Mage_Webhook_Model_Job', $collection->getModelName());
    }

    public function testNewEventInNewCollection()
    {
        $job1 = Mage::getObjectManager()->create('Mage_Webhook_Model_Job')
            ->setSubscriptionId($this->_subscription->getId())
            ->setEventId($this->_event->getId())
            ->save();

        /** @var Mage_Webhook_Model_Resource_Job_Collection $collection */
        $collection = Mage::getObjectManager()->create('Mage_Webhook_Model_Resource_Job_Collection');
        $this->assertEquals(1, count($collection->getItems()));
        $this->assertEquals($job1->getId(), $collection->getFirstItem()->getId());

        $job2 = Mage::getObjectManager()->create('Mage_Webhook_Model_Job')
            ->setSubscriptionId($this->_subscription->getId())
            ->setEventId($this->_event->getId())
            ->save();

        /** @var Mage_Webhook_Model_Resource_Job_Collection $collectionSecond */
        $collectionSecond = Mage::getObjectManager()->create('Mage_Webhook_Model_Resource_Job_Collection');
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
     * @expectedException Zend_Db_Statement_Exception
     * @expectedMessage SQLSTATE[HY000]: General error: 1205 Lock wait timeout exceeded; try restarting transaction
     */
    public function testParallelTransactions()
    {
        $job = Mage::getObjectManager()->create('Mage_Webhook_Model_Job')
            ->setSubscriptionId($this->_subscription->getId())
            ->setEventId($this->_event->getId())
            ->save();
        $job2 = Mage::getObjectManager()->create('Mage_Webhook_Model_Job')
            ->setSubscriptionId($this->_subscription->getId())
            ->setEventId($this->_event->getId())
            ->save();
        $job3 = Mage::getObjectManager()->create('Mage_Webhook_Model_Job')
            ->setSubscriptionId($this->_subscription->getId())
            ->setEventId($this->_event->getId())
            ->setStatus(Magento_PubSub_JobInterface::IN_PROGRESS)
            ->save();

        /** @var Mage_Webhook_Model_Resource_Job_Collection $collection */
        $collection = Mage::getObjectManager()->create('Mage_Webhook_Model_Resource_Job_Collection');

        $beforeLoad = new ReflectionMethod(
            'Mage_Webhook_Model_Resource_Job_Collection', '_beforeLoad');
        $beforeLoad->setAccessible(true);
        $beforeLoad->invoke($collection);
        $data = $collection->getData();
        $this->assertEquals(2, count($data));

        /** @var Mage_Core_Model_Resource $resource */
        $resource = Mage::getObjectManager()->create('Mage_Core_Model_Resource');
        $connection = $resource->getConnection('core_write');

        /** @var Mage_Webhook_Model_Resource_Job_Collection $collection2 */
        $collection2 = Mage::getObjectManager()->create('Mage_Webhook_Model_Resource_Job_Collection');
        $collection2->setConnection($connection);
        $initSelect = new ReflectionMethod(
            'Mage_Webhook_Model_Resource_Job_Collection', '_initSelect');
        $initSelect->setAccessible(true);
        $initSelect->invoke($collection2);


        $afterLoad = new ReflectionMethod(
            'Mage_Webhook_Model_Resource_Job_Collection', '_afterLoad');
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
}