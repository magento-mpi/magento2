<?php
/**
 * Magento_Webhook_Model_Resource_Event_Collection
 *
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Resource_Event_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
    }

    public function testInit()
    {
        /** @var Magento_Webhook_Model_Resource_Event_Collection $collection */
        $collection = $this->_objectManager->create('Magento_Webhook_Model_Resource_Event_Collection');
        $this->assertEquals('Magento_Webhook_Model_Resource_Event', $collection->getResourceModelName());
        $this->assertEquals('Magento_Webhook_Model_Event', $collection->getModelName());

        /* check FOR UPDATE lock */
        $forUpdate = $collection->getSelect()->getPart(Zend_Db_Select::FOR_UPDATE);
        $this->assertTrue($forUpdate);

        $where = array("(`status` = '" . \Magento\PubSub\EventInterface::STATUS_READY_TO_SEND . "')");
        $this->assertEquals($where, $collection->getSelect()->getPart(Zend_Db_Select::WHERE));
    }

    public function testGetData()
    {
        $event = $this->_objectManager->create('Magento_Webhook_Model_Event')->save();

        /** @var Magento_Webhook_Model_Resource_Event_Collection $collection */
        $collection = $this->_objectManager->create('Magento_Webhook_Model_Resource_Event_Collection');
        $this->assertEquals(1, count($collection->getItems()));

        /** @var Magento_Webhook_Model_Resource_Event_Collection $collectionSecond */
        $collectionSecond = $this->_objectManager->create('Magento_Webhook_Model_Resource_Event_Collection');
        $this->assertEquals(0, count($collectionSecond->getItems()));

        $updatedEvent = $this->_objectManager->create('Magento_Webhook_Model_Event')
            ->load($event->getId());

        $this->assertEquals(\Magento\PubSub\EventInterface::STATUS_IN_PROGRESS, $updatedEvent->getStatus());
        $event->delete();
    }

    public function testNewEventInNewCollection()
    {
        $event1 = $this->_objectManager->create('Magento_Webhook_Model_Event')->save();

        /** @var Magento_Webhook_Model_Resource_Event_Collection $collection */
        $collection = $this->_objectManager->create('Magento_Webhook_Model_Resource_Event_Collection');
        $this->assertEquals(1, count($collection->getItems()));
        $this->assertEquals($event1->getId(), $collection->getFirstItem()->getId());

        $event2 = $this->_objectManager->create('Magento_Webhook_Model_Event')->save();

        /** @var Magento_Webhook_Model_Resource_Event_Collection $collectionSecond */
        $collectionSecond = $this->_objectManager->create('Magento_Webhook_Model_Resource_Event_Collection');
        $this->assertEquals(1, count($collectionSecond->getItems()));
        $this->assertEquals($event2->getId(), $collectionSecond->getFirstItem()->getId(),
            sprintf("Event #%s is expected in second collection,"
                    . "found event #%s. It could lead to race conditions issue if it is #%s",
            $event2->getId(), $collectionSecond->getFirstItem()->getId(), $event1->getId())
        );

        $event1->delete();
        $event2->delete();
    }

    public function testRevokeIdlingInProgress()
    {
        /** @var Magento_Webhook_Model_Resource_Event_Collection $collection */
        $collection = $this->_objectManager->create('Magento_Webhook_Model_Resource_Event_Collection');
        $this->assertNull($collection->revokeIdlingInProgress());
    }

    /**
     * Emulates concurrent transactions. Executes 50 seconds because of lock timeout
     *
     * @expectedException Zend_Db_Statement_Exception
     * @expectedMessage SQLSTATE[HY000]: General error: 1205 Lock wait timeout exceeded; try restarting transaction
     */
    public function testParallelTransactions()
    {
        $event = $this->_objectManager->create('Magento_Webhook_Model_Event')->save();
        $event2 = $this->_objectManager->create('Magento_Webhook_Model_Event')->save();
        /** @var Magento_Webhook_Model_Event $event3 */
        $event3 = $this->_objectManager->create('Magento_Webhook_Model_Event')
            ->setStatus(\Magento\PubSub\EventInterface::STATUS_IN_PROGRESS)
            ->save();

        /** @var Magento_Webhook_Model_Resource_Event_Collection $collection */
        $collection = $this->_objectManager->create('Magento_Webhook_Model_Resource_Event_Collection');



        $beforeLoad = new ReflectionMethod(
            'Magento_Webhook_Model_Resource_Event_Collection', '_beforeLoad');
        $beforeLoad->setAccessible(true);
        $beforeLoad->invoke($collection);
        $data = $collection->getData();
        $this->assertEquals(2, count($data));

        /** @var Magento_Core_Model_Resource $resource */
        $resource = $this->_objectManager->create('Magento_Core_Model_Resource');
        $connection = $resource->getConnection('core_write');

        /** @var Magento_Webhook_Model_Resource_Event_Collection $collection2 */
        $collection2 = $this->_objectManager->create('Magento_Webhook_Model_Resource_Event_Collection');
        $collection2->setConnection($connection);
        $initSelect = new ReflectionMethod(
            'Magento_Webhook_Model_Resource_Event_Collection', '_initSelect');
        $initSelect->setAccessible(true);
        $initSelect->invoke($collection2);


        $afterLoad = new ReflectionMethod(
            'Magento_Webhook_Model_Resource_Event_Collection', '_afterLoad');
        $afterLoad->setAccessible(true);


        try {
            $collection2->getData();
        } catch (Zend_Db_Statement_Exception $e) {
            $event->delete();
            $event2->delete();
            $event3->delete();
            $afterLoad->invoke($collection);

            throw ($e);
        }
        $event->delete();
        $event2->delete();
        $event3->delete();
        $afterLoad->invoke($collection);
    }
}
