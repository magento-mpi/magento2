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
     * @magentoConfigFixture global/resources/db/table_prefix prefix_
     */
    public function testInit()
    {
        /** @var Magento_Webhook_Model_Resource_Event $eventResource */
        $eventResource = Mage::getObjectManager()->create('Magento_Webhook_Model_Resource_Event');
        $this->assertEquals('prefix_webhook_event', $eventResource->getMainTable());
        $this->assertEquals('event_id', $eventResource->getIdFieldName());
    }
}
