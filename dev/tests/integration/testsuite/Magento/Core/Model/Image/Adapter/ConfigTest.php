<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Image\Adapter;

class ConfigTest extends \PHPUnit_Framework_TestCase
{

    public function testGetAdapterName()
    {
        /** @var Config $config */
        $config = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Image\Adapter\Config');
        $this->assertEquals('Magento\Image\Adapter\AdapterInterface', $config->getAdapterName());
    }
}
