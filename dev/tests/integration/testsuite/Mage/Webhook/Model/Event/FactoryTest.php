<?php
/**
 * Mage_Webhook_Model_Event_Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Event_FactoryTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $factory = Mage::getObjectManager()->create('Mage_Webhook_Model_Event_Factory');
        $data = array('array', 'of', 'event', 'data');
        $topic = 'Topic on which to publish data';

        /** @var Mage_Webhook_Model_Event $event */
        $event = $factory->create($topic, $data);

        $this->assertInstanceOf('Mage_Webhook_Model_Event', $event);
        $this->assertEquals($topic, $event->getTopic());
        $this->assertEquals($data, $event->getBodyData());
    }

    public function testCreateEmpty()
    {
        $factory = Mage::getObjectManager()->create('Mage_Webhook_Model_Event_Factory');

        /** @var Mage_Webhook_Model_Event $event */
        $event = $factory->create('', array());

        $this->assertInstanceOf('Mage_Webhook_Model_Event', $event);
        $this->assertEmpty($event->getBodyData());
        $this->assertEmpty($event->getTopic());
    }
}