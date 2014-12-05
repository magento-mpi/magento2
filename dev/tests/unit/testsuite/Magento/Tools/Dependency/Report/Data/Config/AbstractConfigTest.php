<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Dependency\Report\Data\Config;

class AbstractConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testGetModules()
    {
        $modules = array('foo', 'baz', 'bar');

        /** @var \Magento\Tools\Dependency\Report\Data\Config\AbstractConfig $config */
        $config = $this->getMockForAbstractClass(
            'Magento\Tools\Dependency\Report\Data\Config\AbstractConfig',
            array('modules' => $modules)
        );

        $this->assertEquals($modules, $config->getModules());
    }
}
