<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Tools\Dependency;

use Magento\Tools\Dependency\Circular;

class CircularTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tools\Dependency\Circular
     */
    protected $circular;

    protected function setUp()
    {
        $this->circular = new Circular();
    }

    public function testBuildCircularDependencies()
    {
        $dependencies = array(1 => array(2), 2 => array(3, 5), 3 => array(1), 5 => array(2));
        $expectedCircularDependencies = array(
            1 => array(array(1, 2, 3, 1)),
            2 => array(array(2, 3, 1, 2), array(2, 5, 2)),
            3 => array(array(3, 1, 2, 3)),
            5 => array(array(5, 2, 5))
        );
        $this->assertEquals($expectedCircularDependencies, $this->circular->buildCircularDependencies($dependencies));
    }
}
