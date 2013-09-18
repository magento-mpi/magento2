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
    public function testConstructor()
    {
        /** @var Magento_Core_Model_Event_Manager $eventManager */
        $eventManager = $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false);
        /** @var Magento_Data_Collection_Db_FetchStrategyInterface $mockFetchStrategy */
        $mockFetchStrategy = $this->getMockBuilder('Magento_Data_Collection_Db_FetchStrategyInterface')
            ->disableOriginalConstructor()
            ->getMock();
        /** @var Magento_Core_Model_EntityFactory $entityFactory */
        $entityFactory = $this->getMock('Magento_Core_Model_EntityFactory', array(), array(), '', false);
        $mockDBAdapter = $this->getMockBuilder('Magento_DB_Adapter_Pdo_Mysql')
            ->disableOriginalConstructor()
            ->setMethods(array('_connect', '_quote'))
            ->getMockForAbstractClass();
        $mockResourceEvent = $this->getMockBuilder('Magento_Webhook_Model_Resource_Job')
            ->disableOriginalConstructor()
            ->getMock();
        $mockResourceEvent->expects($this->once())
            ->method('getReadConnection')
            ->will($this->returnValue($mockDBAdapter));

        $collection = new Magento_Webhook_Model_Resource_Job_Collection(
            $eventManager, $mockFetchStrategy, $entityFactory, $mockResourceEvent
        );
        $this->assertInstanceOf('Magento_Webhook_Model_Resource_Job_Collection', $collection);
        $this->assertEquals('Magento_Webhook_Model_Resource_Job', $collection->getResourceModelName());
    }
}
