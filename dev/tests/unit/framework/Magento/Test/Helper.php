<?php
/**
 * Framework for unit tests containing helper methods
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * Number of fields is necessary because of the number of fields used by multiple layers
 * of parent classes.
 *
 */
namespace Magento\Test;

use Magento\TestFramework\Helper\ObjectManager;

class Helper extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
    }

    /**
     * Generate a basic method stub
     *
     * @param \PHPUnit_Framework_MockObject_MockObject $mock
     * @param string $method
     *
     * @return \PHPUnit_Framework_MockObject_Builder_InvocationMocker
     */
    protected function basicStub($mock, $method)
    {
        return $mock->expects($this->any())
            ->method($method)
            ->withAnyParameters();
    }

    /**
     * Build a basic mock object
     *
     * @param string $className
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function basicMock($className)
    {
        return $this->getMockBuilder($className)
            ->disableOriginalConstructor()
            ->getMock();
    }
}