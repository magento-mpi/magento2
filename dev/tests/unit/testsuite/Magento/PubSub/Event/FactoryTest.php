<?php
/**
 * \Magento\PubSub\Event\Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PubSub\Event;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $factory = new \Magento\PubSub\Event\Factory();
        $event = $factory->create('topic', array());

        $this->assertInstanceOf('Magento\PubSub\EventInterface', $event);
    }

}
