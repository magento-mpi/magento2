<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Dependency\Report\Framework\Data;

use Magento\TestFramework\Helper\ObjectManager;

class ModuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $name
     * @param array $dependencies
     * @return \Magento\Tools\Dependency\Report\Framework\Data\Module
     */
    protected function createModule($name, $dependencies = array())
    {
        $objectManagerHelper = new ObjectManager($this);
        return $objectManagerHelper->getObject(
            'Magento\Tools\Dependency\Report\Framework\Data\Module',
            array('name' => $name, 'dependencies' => $dependencies)
        );
    }

    public function testGetName()
    {
        $name = 'name';
        $module = $this->createModule($name, array());

        $this->assertEquals($name, $module->getName());
    }

    public function testGetDependencies()
    {
        $dependencies = array('foo', 'baz', 'bar');
        $module = $this->createModule('name', $dependencies);

        $this->assertEquals($dependencies, $module->getDependencies());
    }

    public function testGetDependenciesCount()
    {
        $module = $this->createModule('name', array('foo', 'baz', 'bar'));

        $this->assertEquals(3, $module->getDependenciesCount());
    }
}
