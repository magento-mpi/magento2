<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Test\Tools\Dependency;

use Magento\TestFramework\Helper\ObjectManager;

class ModuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $name
     * @param array $dependencies
     * @return \Magento\Tools\Dependency\Module
     */
    protected function createModule($name, $dependencies)
    {
        $objectManagerHelper = new ObjectManager($this);
        return $objectManagerHelper->getObject('Magento\Tools\Dependency\Module', [
            'name' => $name,
            'dependencies' => $dependencies,
        ]);
    }

    public function testAccessor()
    {
        $name = 'name';
        $dependencies = ['foo', 'bar', 'baz'];

        $module = $this->createModule($name, $dependencies);

        $this->assertEquals($name, $module->getName());
        $this->assertEquals($dependencies, $module->getDependencies());
    }

    /**
     * @param array $dependencies
     * @param bool $result
     * @dataProvider dataProviderHasDependencies
     */
    public function testHasDependencies($dependencies, $result)
    {
        $module = $this->createModule('name', $dependencies);

        $this->assertEquals($result, $module->hasDependencies());
    }

    /**
     * @return array
     */
    public function dataProviderHasDependencies()
    {
        return [
            [['foo', 'bar', 'baz'], true],
            [[], false],
        ];
    }

    public function testGetDependenciesCount()
    {
        $module = $this->createModule('name', ['foo', 'bar', 'baz']);

        $this->assertEquals(3, $module->getDependenciesCount());
    }

    public function testDependenciesCountByType()
    {
        $dependencyFirst = $this->getMock('Magento\Tools\Dependency\Dependency', [], [], '', false);
        $dependencyFirst->expects($this->once())->method('isHard')->will($this->returnValue(true));

        $dependencySecond = $this->getMock('Magento\Tools\Dependency\Dependency', [], [], '', false);
        $dependencySecond->expects($this->once())->method('isHard')->will($this->returnValue(true));

        $dependencyThird = $this->getMock('Magento\Tools\Dependency\Dependency', [], [], '', false);
        $dependencyThird->expects($this->once())->method('isSoft')->will($this->returnValue(true));

        $module = $this->createModule('name', [$dependencyFirst, $dependencySecond, $dependencyThird]);
        $this->assertEquals(2, $module->getHardDependenciesCount());
        $this->assertEquals(1, $module->getSoftDependenciesCount());
    }
}
