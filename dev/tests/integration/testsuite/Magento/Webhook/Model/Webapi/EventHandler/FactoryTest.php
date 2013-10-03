<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model\Webapi\EventHandler;

/**
 * \Magento\Webhook\Model\Webapi\EventHandler\Factory
 *
 * @magentoDbIsolation enabled
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $factory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Webhook\Model\Webapi\EventHandler\Factory');
        $eventHandler = $factory->create();
        $this->assertInstanceOf('Magento\Webhook\Model\Webapi\EventHandler', $eventHandler);
    }
}
