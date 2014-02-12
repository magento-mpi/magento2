<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Test\Tools\Dependency\Report\Circular\Data;

use Magento\TestFramework\Helper\ObjectManager;

class ModuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $name
     * @param array $chains
     * @return \Magento\Tools\Dependency\Report\Circular\Data\Module
     */
    protected function createModule($name, $chains = array())
    {
        $objectManagerHelper = new ObjectManager($this);
        return $objectManagerHelper->getObject('Magento\Tools\Dependency\Report\Circular\Data\Module', [
            'name' => $name,
            'chains' => $chains,
        ]);
    }

    public function testGetters()
    {
        $name = 'name';
        $chains = ['foo', 'baz', 'bar'];
        $module = $this->createModule($name, $chains);

        $this->assertEquals($name, $module->getName());
        $this->assertEquals($chains, $module->getChains());
    }

    public function testGetChainsCount()
    {
        $module = $this->createModule('name', ['foo', 'baz', 'bar']);

        $this->assertEquals(3, $module->getChainsCount());
    }
}
