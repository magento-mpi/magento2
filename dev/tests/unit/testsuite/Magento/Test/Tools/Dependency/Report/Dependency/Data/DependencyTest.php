<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Test\Tools\Dependency\Report\Dependency\Data;

use Magento\TestFramework\Helper\ObjectManager;
use Magento\Tools\Dependency\Report\Dependency\Data\Dependency;

class DependencyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $module
     * @param string|null $type One of \Magento\Tools\Dependency\Dependency::TYPE_ const
     * @return \Magento\Tools\Dependency\Report\Dependency\Data\Dependency
     */
    protected function createDependency($module, $type = null)
    {
        $objectManagerHelper = new ObjectManager($this);
        return $objectManagerHelper->getObject(
            'Magento\Tools\Dependency\Report\Dependency\Data\Dependency',
            array('module' => $module, 'type' => $type)
        );
    }

    public function testGetModule()
    {
        $module = 'module';

        $dependency = $this->createDependency($module);

        $this->assertEquals($module, $dependency->getModule());
    }

    public function testGetType()
    {
        $type = Dependency::TYPE_SOFT;

        $dependency = $this->createDependency('module', $type);

        $this->assertEquals($type, $dependency->getType());
    }

    public function testThatHardTypeIsDefault()
    {
        $dependency = $this->createDependency('module');

        $this->assertEquals(Dependency::TYPE_HARD, $dependency->getType());
    }

    public function testThatHardTypeIsDefaultIfPassedWrongType()
    {
        $dependency = $this->createDependency('module', 'wrong_type');

        $this->assertEquals(Dependency::TYPE_HARD, $dependency->getType());
    }
}
