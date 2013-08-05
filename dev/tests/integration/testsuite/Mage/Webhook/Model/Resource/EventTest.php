<?php
/**
 * Mage_Webhook_Model_Resource_Event
 *
 * {license_notice}
 *
 * @magentoDbIsolation enabled
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Resource_EventTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoConfigFixture global/resources/db/table_prefix prefix_
     */
    public function testInit()
    {
        /** @var Mage_Webhook_Model_Resource_Event $eventResource */
        $eventResource = Mage::getObjectManager()->create('Mage_Webhook_Model_Resource_Event');
        $this->assertEquals('prefix_webhook_event', $eventResource->getMainTable());
        $this->assertEquals('event_id', $eventResource->getIdFieldName());
    }
}