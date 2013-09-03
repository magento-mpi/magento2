<?php
/**
 * Magento_Webhook_Model_Event_Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Event_FactoryTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $factory = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create('Magento_Webhook_Model_Event_Factory');
        $data = array('array', 'of', 'event', 'data');
        $topic = 'Topic on which to publish data';

        /** @var Magento_Webhook_Model_Event $event */
        $event = $factory->create($topic, $data);

        $this->assertInstanceOf('Magento_Webhook_Model_Event', $event);
        $this->assertEquals($topic, $event->getTopic());
        $this->assertEquals($data, $event->getBodyData());
    }

    public function testCreateEmpty()
    {
        $factory = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create('Magento_Webhook_Model_Event_Factory');

        /** @var Magento_Webhook_Model_Event $event */
        $event = $factory->create('', array());

        $this->assertInstanceOf('Magento_Webhook_Model_Event', $event);
        $this->assertEmpty($event->getBodyData());
        $this->assertEmpty($event->getTopic());
    }
}
