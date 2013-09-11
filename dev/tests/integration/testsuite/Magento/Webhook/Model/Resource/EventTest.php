<?php
/**
 * \Magento\Webhook\Model\Resource\Event
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
        /** @var \Magento\Webhook\Model\Resource\Event $eventResource */
        $eventResource = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento\Webhook\Model\Resource\Event');
        $this->assertEquals('prefix_webhook_event', $eventResource->getMainTable());
        $this->assertEquals('event_id', $eventResource->getIdFieldName());
    }
}
