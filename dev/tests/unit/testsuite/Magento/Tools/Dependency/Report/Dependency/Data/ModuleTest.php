<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Dependency\Report\Dependency\Data;

use Magento\TestFramework\Helper\ObjectManager;

class ModuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tools\Dependency\Report\Dependency\Data\Dependency|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dependencyFirst;

    /**
     * @var \Magento\Tools\Dependency\Report\Dependency\Data\Dependency|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dependencySecond;

    /**
     * @var \Magento\Tools\Dependency\Report\Dependency\Data\Module
     */
    protected $module;

    public function setUp()
    {
        $this->dependencyFirst = $this->getMock(
            'Magento\Tools\Dependency\Report\Dependency\Data\Dependency',
            array(),
            array(),
            '',
            false
        );
        $this->dependencySecond = $this->getMock(
            'Magento\Tools\Dependency\Report\Dependency\Data\Dependency',
            array(),
            array(),
            '',
            false
        );

        $objectManagerHelper = new ObjectManager($this);
        $this->module = $objectManagerHelper->getObject(
            'Magento\Tools\Dependency\Report\Dependency\Data\Module',
            array('name' => 'name', 'dependencies' => array($this->dependencyFirst, $this->dependencySecond))
        );
    }

    public function testGetName()
    {
        $this->assertEquals('name', $this->module->getName());
    }

    public function testGetDependencies()
    {
        $this->assertEquals(array($this->dependencyFirst, $this->dependencySecond), $this->module->getDependencies());
    }

    public function testGetDependenciesCount()
    {
        $this->assertEquals(2, $this->module->getDependenciesCount());
    }

    public function testGetHardDependenciesCount()
    {
        $this->dependencyFirst->expects($this->once())->method('isHard')->will($this->returnValue(true));
        $this->dependencyFirst->expects($this->never())->method('isSoft');

        $this->dependencySecond->expects($this->once())->method('isHard')->will($this->returnValue(false));
        $this->dependencySecond->expects($this->never())->method('isSoft');

        $this->assertEquals(1, $this->module->getHardDependenciesCount());
    }

    public function testGetSoftDependenciesCount()
    {
        $this->dependencyFirst->expects($this->never())->method('isHard');
        $this->dependencyFirst->expects($this->once())->method('isSoft')->will($this->returnValue(true));

        $this->dependencySecond->expects($this->never())->method('isHard');
        $this->dependencySecond->expects($this->once())->method('isSoft')->will($this->returnValue(false));

        $this->assertEquals(1, $this->module->getSoftDependenciesCount());
    }
}
