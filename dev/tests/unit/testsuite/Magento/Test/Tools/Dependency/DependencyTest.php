<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Test\Tools\Dependency;

use Magento\TestFramework\Helper\ObjectManager;
use Magento\Tools\Dependency\Dependency;

class DependencyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $module
     * @param string|null $type One of \Magento\Tools\Dependency\Dependency::TYPE_ const
     * @return \Magento\Tools\Dependency\Dependency
     */
    protected function createDependency($module, $type = null)
    {
        $objectManagerHelper = new ObjectManager($this);
        return $objectManagerHelper->getObject('Magento\Tools\Dependency\Dependency', [
            'module' => $module,
            'type' => $type,
        ]);
    }

    public function testAccessor()
    {
        $module = 'module';
        $type = Dependency::TYPE_SOFT;

        $dependency = $this->createDependency($module, $type);

        $this->assertEquals($module, $dependency->getModule());
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
