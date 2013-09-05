<?php
/**
 * \Magento\PubSub\Event\Factory
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
        $factory = new \Magento\PubSub\Event\Factory();
        $event = $factory->create('topic', array());

        $this->assertInstanceOf('\Magento\PubSub\EventInterface', $event);
    }
}