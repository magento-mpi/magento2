<?php
/**
 * \Magento\Webhook\Model\Resource\Event\Collection
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
    public function testConstructor()
    {
        /** @var Magento_Core_Model_Event_Manager $eventManager */
        $eventManager = $this->getMock('Magento\Core\Model\Event\Manager', array(), array(), '', false);
        /** @var Magento_Data_Collection_Db_FetchStrategyInterface $mockFetchStrategy */
        $mockFetchStrategy = $this->getMockBuilder('Magento\Data\Collection\Db\FetchStrategyInterface')
            ->disableOriginalConstructor()
            ->getMock();
        /** @var Magento_Core_Model_EntityFactory $entityFactory */
        $entityFactory = $this->getMock('Magento_Core_Model_EntityFactory', array(), array(), '', false);

        $mockDBAdapter = $this->getMockBuilder('Magento\DB\Adapter\Pdo\Mysql')
            ->disableOriginalConstructor()
            ->setMethods(array('_connect', '_quote'))
            ->getMockForAbstractClass();
        $mockResourceEvent = $this->getMockBuilder('Magento\Webhook\Model\Resource\Event')
            ->disableOriginalConstructor()
            ->getMock();
        $mockResourceEvent->expects($this->any())
            ->method('getReadConnection')
            ->will($this->returnValue($mockDBAdapter));
        $logger = $this->getMock('Magento_Core_Model_Logger', array(), array(), '', false);

        $collection = new Magento_Webhook_Model_Resource_Event_Collection(
            $eventManager, $logger, $mockFetchStrategy, $entityFactory, $mockResourceEvent
        );
        $this->assertInstanceOf('Magento\Webhook\Model\Resource\Event\Collection', $collection);
        $this->assertEquals('Magento\Webhook\Model\Resource\Event', $collection->getResourceModelName());
        $this->assertEquals('Magento\Webhook\Model\Event', $collection->getModelName());
    }
}
