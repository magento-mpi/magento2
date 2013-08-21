<?php
/**
 * Magento_Webhook_Model_Resource_Event
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Resource_EventTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $tableName = 'webhook_event_table';
        $idFieldName = 'event_id';

        $resourceMock = $this->getMockBuilder('Magento_Core_Model_Resource')
            ->disableOriginalConstructor()
            ->getMock();
        $resourceMock->expects($this->once())
            ->method('getTableName')
            ->with('webhook_event')
            ->will($this->returnValue($tableName));

        $event = new Magento_Webhook_Model_Resource_Event ($resourceMock);
        $this->assertEquals($tableName, $event->getMainTable() );
        $this->assertEquals($idFieldName, $event->getIdFieldName());
    }
}
