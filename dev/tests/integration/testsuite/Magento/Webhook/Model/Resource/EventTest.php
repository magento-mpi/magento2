<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model\Resource;

/**
 * \Magento\Webhook\Model\Resource\Event
 *
 * @magentoDbIsolation enabled
 */
class EventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoConfigFixture global/resources/db/table_prefix prefix_
     */
    public function testInit()
    {
        /** @var \Magento\Webhook\Model\Resource\Event $eventResource */
        $eventResource = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Webhook\Model\Resource\Event');
        $this->assertEquals('prefix_webhook_event', $eventResource->getMainTable());
        $this->assertEquals('event_id', $eventResource->getIdFieldName());
    }
}
