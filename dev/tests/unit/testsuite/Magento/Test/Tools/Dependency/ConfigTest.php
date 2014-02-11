<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Test\Tools\Dependency;

use Magento\TestFramework\Helper\ObjectManager;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $modules
     * @return \Magento\Tools\Dependency\Config
     */
    protected function createConfig($modules)
    {
        $objectManagerHelper = new ObjectManager($this);
        return $objectManagerHelper->getObject('Magento\Tools\Dependency\Config', [
            'modules' => $modules,
        ]);
    }

    public function testAccessor()
    {
        $modules = ['foo', 'bar', 'baz'];

        $config = $this->createConfig($modules);

        $this->assertEquals($modules, $config->getModules());
    }

    /**
     * @param int $countHardDependencies
     * @param int $countSoftDependencies
     * @param bool $result
     * @dataProvider dataProviderHasDependencies
     */
    public function testHasDependencies($countHardDependencies, $countSoftDependencies, $result)
    {
        $module = $this->getMock('Magento\Tools\Dependency\Module', [], [], '', false);
        $module->expects($this->any())->method('getHardDependenciesCount')
            ->will($this->returnValue($countHardDependencies));
        $module->expects($this->any())->method('getSoftDependenciesCount')
            ->will($this->returnValue($countSoftDependencies));

        $config = $this->createConfig([$module]);

        $this->assertEquals($result, $config->hasDependencies());
    }

    /**
     * @return array
     */
    public function dataProviderHasDependencies()
    {
        return [
            [1, 1, true],
            [1, 0, true],
            [0, 1, true],
            [0, 0, false],
        ];
    }

    public function testGetDependenciesCount()
    {
        $moduleFirst = $this->getMock('Magento\Tools\Dependency\Module', [], [], '', false);
        $moduleFirst->expects($this->once())->method('getHardDependenciesCount')->will($this->returnValue(1));
        $moduleFirst->expects($this->once())->method('getSoftDependenciesCount')->will($this->returnValue(2));

        $moduleSecond = $this->getMock('Magento\Tools\Dependency\Module', [], [], '', false);
        $moduleSecond->expects($this->once())->method('getHardDependenciesCount')->will($this->returnValue(3));
        $moduleSecond->expects($this->once())->method('getSoftDependenciesCount')->will($this->returnValue(4));

        $config = $this->createConfig([$moduleFirst, $moduleSecond]);

        $this->assertEquals(10, $config->getDependenciesCount());
    }
}
