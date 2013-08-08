<?php
/**
 * Mage_Webhook_Model_Resource_Event
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Resource_EventTest extends PHPUnit_Framework_TestCase
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

        $event = new Mage_Webhook_Model_Resource_Event ($resourceMock);
        $this->assertEquals($tableName, $event->getMainTable() );
        $this->assertEquals($idFieldName, $event->getIdFieldName());
    }
}