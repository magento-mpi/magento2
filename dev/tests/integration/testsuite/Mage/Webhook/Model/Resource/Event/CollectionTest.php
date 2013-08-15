<?php
/**
 * Mage_Webhook_Model_Resource_Event_Collection
 *
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Resource_Event_CollectionTest extends PHPUnit_Framework_TestCase
{
    public function testInit()
    {
        /** @var Mage_Webhook_Model_Resource_Event_Collection $collection */
        $collection = Mage::getObjectManager()->create('Mage_Webhook_Model_Resource_Event_Collection');
        $this->assertEquals('Mage_Webhook_Model_Resource_Event', $collection->getResourceModelName());
        $this->assertEquals('Mage_Webhook_Model_Event', $collection->getModelName());

        /* check FOR UPDATE lock */
        $forUpdate = $collection->getSelect()->getPart(Zend_Db_Select::FOR_UPDATE);
        $this->assertTrue($forUpdate);

        $where = array("(`status` = '" . Magento_PubSub_EventInterface::READY_TO_SEND . "')");
        $this->assertEquals($where, $collection->getSelect()->getPart(Zend_Db_Select::WHERE));
    }

    public function testGetData()
    {
        $event = Mage::getObjectManager()->create('Mage_Webhook_Model_Event')
            ->save();

        /** @var Mage_Webhook_Model_Resource_Event_Collection $collection */
        $collection = Mage::getObjectManager()->create('Mage_Webhook_Model_Resource_Event_Collection');
        $this->assertEquals(1, count($collection->getItems()));

        /** @var Mage_Webhook_Model_Resource_Event_Collection $collectionSecond */
        $collectionSecond = Mage::getObjectManager()->create('Mage_Webhook_Model_Resource_Event_Collection');
        $this->assertEquals(0, count($collectionSecond->getItems()));

        $updatedEvent = Mage::getObjectManager()->create('Mage_Webhook_Model_Event')
            ->load($event->getId());

        $this->assertEquals(Magento_PubSub_EventInterface::IN_PROGRESS, $updatedEvent->getStatus());
        $event->delete();
    }

    public function testNewEventInNewCollection()
    {
        $event1 = Mage::getObjectManager()->create('Mage_Webhook_Model_Event')
            ->save();

        /** @var Mage_Webhook_Model_Resource_Event_Collection $collection */
        $collection = Mage::getObjectManager()->create('Mage_Webhook_Model_Resource_Event_Collection');
        $this->assertEquals(1, count($collection->getItems()));
        $this->assertEquals($event1->getId(), $collection->getFirstItem()->getId());

        $event2 = Mage::getObjectManager()->create('Mage_Webhook_Model_Event')
            ->save();

        /** @var Mage_Webhook_Model_Resource_Event_Collection $collectionSecond */
        $collectionSecond = Mage::getObjectManager()->create('Mage_Webhook_Model_Resource_Event_Collection');
        $this->assertEquals(1, count($collectionSecond->getItems()));
        $this->assertEquals($event2->getId(), $collectionSecond->getFirstItem()->getId(),
            sprintf("Event #%s is expected in second collection,"
                    . "found event #%s. It could lead to race conditions issue if it is #%s",
            $event2->getId(), $collectionSecond->getFirstItem()->getId(), $event1->getId())
        );

        $event1->delete();
        $event2->delete();
    }

    /**
     * @expectedException Zend_Db_Statement_Exception
     * @expectedMessage SQLSTATE[HY000]: General error: 1205 Lock wait timeout exceeded; try restarting transaction
     */
    public function testParallelTransactions()
    {
        $event = Mage::getObjectManager()->create('Mage_Webhook_Model_Event')
            ->save();
        $event2 = Mage::getObjectManager()->create('Mage_Webhook_Model_Event')
            ->save();
        /** @var Mage_Webhook_Model_Event $event3 */
        $event3 = Mage::getObjectManager()->create('Mage_Webhook_Model_Event')
            ->setStatus(Magento_PubSub_EventInterface::IN_PROGRESS)
            ->save();

        /** @var Mage_Webhook_Model_Resource_Event_Collection $collection */
        $collection = Mage::getObjectManager()->create('Mage_Webhook_Model_Resource_Event_Collection');



        $beforeLoad = new ReflectionMethod(
            'Mage_Webhook_Model_Resource_Event_Collection', '_beforeLoad');
        $beforeLoad->setAccessible(true);
        $beforeLoad->invoke($collection);
        $data = $collection->getData();
        $this->assertEquals(2, count($data));

        /** @var Mage_Core_Model_Resource $resource */
        $resource = Mage::getObjectManager()->create('Mage_Core_Model_Resource');
        $connection = $resource->getConnection('core_write');

        /** @var Mage_Webhook_Model_Resource_Event_Collection $collection2 */
        $collection2 = Mage::getObjectManager()->create('Mage_Webhook_Model_Resource_Event_Collection');
        $collection2->setConnection($connection);
        $initSelect = new ReflectionMethod(
            'Mage_Webhook_Model_Resource_Event_Collection', '_initSelect');
        $initSelect->setAccessible(true);
        $initSelect->invoke($collection2);


        $afterLoad = new ReflectionMethod(
            'Mage_Webhook_Model_Resource_Event_Collection', '_afterLoad');
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