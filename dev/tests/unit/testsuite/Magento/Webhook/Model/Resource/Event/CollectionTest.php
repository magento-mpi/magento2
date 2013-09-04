<?php
/**
 * Magento_Webhook_Model_Resource_Event_Collection
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
        $mockFetchStrategy = $this->getMockBuilder('Magento_Data_Collection_Db_FetchStrategyInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $mockDBAdapter = $this->getMockBuilder('Magento_DB_Adapter_Pdo_Mysql')
            ->disableOriginalConstructor()
            ->setMethods(array('_connect', '_quote'))
            ->getMockForAbstractClass();
        $mockResourceEvent = $this->getMockBuilder('Magento_Webhook_Model_Resource_Event')
            ->disableOriginalConstructor()
            ->getMock();
        $mockResourceEvent->expects($this->any())
            ->method('getReadConnection')
            ->will($this->returnValue($mockDBAdapter));

        $collection = new Magento_Webhook_Model_Resource_Event_Collection($mockFetchStrategy, $mockResourceEvent);
        $this->assertInstanceOf('Magento_Webhook_Model_Resource_Event_Collection', $collection);
        $this->assertEquals('Magento_Webhook_Model_Resource_Event', $collection->getResourceModelName());
        $this->assertEquals('Magento_Webhook_Model_Event', $collection->getModelName());
    }
}
