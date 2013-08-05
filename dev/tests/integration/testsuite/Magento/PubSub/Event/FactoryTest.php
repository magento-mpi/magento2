<?php
/**
 * Magento_PubSub_Event_Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_PubSub_Event_FactoryTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $factory = new Magento_PubSub_Event_Factory();
        $event = $factory->create('topic', array());

        $this->assertInstanceOf('Magento_PubSub_EventInterface', $event);
    }
}