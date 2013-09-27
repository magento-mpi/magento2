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
namespace Magento\Webhook\Model\Resource;

class EventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Magento\Webhook\Model\Resource\Event::init
     */
    public function testInit()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $resource = $objectManager->create('Magento\Core\Model\Resource', array('tablePrefix' => 'prefix_'));

        /** @var \Magento\Webhook\Model\Resource\Event $eventResource */
        $eventResource = $objectManager->create('Magento\Webhook\Model\Resource\Event', array('resource' => $resource));

        $this->assertEquals('prefix_webhook_event', $eventResource->getMainTable());
        $this->assertEquals('event_id', $eventResource->getIdFieldName());
    }
}
