<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Test\Tools\Dependency\Report\Circular\Data;

use Magento\TestFramework\Helper\ObjectManager;

class ChainTest extends \PHPUnit_Framework_TestCase
{
    public function testGetModules()
    {
        $modules = array('foo', 'baz', 'bar');

        $objectManagerHelper = new ObjectManager($this);
        /** @var \Magento\Tools\Dependency\Report\Circular\Data\Chain $chain */
        $chain = $objectManagerHelper->getObject(
            'Magento\Tools\Dependency\Report\Circular\Data\Chain',
            array('modules' => $modules)
        );

        $this->assertEquals($modules, $chain->getModules());
    }
}
