<?php
/**
 * \Magento\Webhook\Model\Event\Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model\Event;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $factory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Webhook\Model\Event\Factory');
        $data = array('array', 'of', 'event', 'data');
        $topic = 'Topic on which to publish data';

        /** @var \Magento\Webhook\Model\Event $event */
        $event = $factory->create($topic, $data);

        $this->assertInstanceOf('Magento\Webhook\Model\Event', $event);
        $this->assertEquals($topic, $event->getTopic());
        $this->assertEquals($data, $event->getBodyData());
    }

    public function testCreateEmpty()
    {
        $factory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Webhook\Model\Event\Factory');

        /** @var \Magento\Webhook\Model\Event $event */
        $event = $factory->create('', array());

        $this->assertInstanceOf('Magento\Webhook\Model\Event', $event);
        $this->assertEmpty($event->getBodyData());
        $this->assertEmpty($event->getTopic());
    }
}
