<?php
/**
 * Magento_Webhook_Model_Resource_Event
 *
 * {license_notice}
 *
 * @magentoDbIsolation enabled
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Resource_EventTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Magento_Webhook_Model_Resource_Event::init
     */
    public function testInit()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $resource = $objectManager->create('Magento_Core_Model_Resource', array('tablePrefix' => 'prefix_'));

        /** @var Magento_Webhook_Model_Resource_Event $eventResource */
        $eventResource = $objectManager->create('Magento_Webhook_Model_Resource_Event', array('resource' => $resource));

        $this->assertEquals('prefix_webhook_event', $eventResource->getMainTable());
        $this->assertEquals('event_id', $eventResource->getIdFieldName());
    }
}
