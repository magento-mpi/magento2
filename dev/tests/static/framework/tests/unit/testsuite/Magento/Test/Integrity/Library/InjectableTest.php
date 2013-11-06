<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Integrity\Library;

use Magento\TestFramework\Integrity\Library\Injectable;

/**
 * @package Magento\Test
 */
class InjectableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Injectable
     */
    protected $injectable;

    /**
     * @var \Zend\Code\Reflection\FileReflection
     */
    protected $fileReflection;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $parameterReflection;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->injectable = new Injectable();
        $this->fileReflection = $this->getMockBuilder('Zend\Code\Reflection\FileReflection')
            ->disableOriginalConstructor()
            ->getMock();

        $classReflection = $this->getMockBuilder('Zend\Code\Reflection\ClassReflection')
            ->disableOriginalConstructor()
            ->getMock();

        $methodReflection = $this->getMockBuilder('Zend\Code\Reflection\MethodReflection')
            ->disableOriginalConstructor()
            ->getMock();

        $this->parameterReflection = $this->getMockBuilder('Zend\Code\Reflection\ParameterReflection')
            ->disableOriginalConstructor()
            ->getMock();

        $methodReflection->expects($this->once())
            ->method('getParameters')
            ->will($this->returnValue(array($this->parameterReflection)));

        $classReflection->expects($this->once())
            ->method('getMethods')
            ->will($this->returnValue(array($methodReflection)));

        $this->fileReflection->expects($this->once())
            ->method('getClasses')
            ->will($this->returnValue(array($classReflection)));
    }

    /**
     * @test
     */
    public function testGetWrongDependencies()
    {
        $this->parameterReflection->expects($this->once())
            ->method('getClass')
            ->will(
                $this->returnCallback(
                    function () {
                        throw new \ReflectionException('Class Magento\Core\Model\Object does not exist');
                    }
                )
            );

        $this->injectable->getWrongDependencies($this->fileReflection);
    }

    /**
     * @test
     * @expectedException \ReflectionException
     */
    public function testGetWrongDependenciesWithOtherException()
    {
        $this->parameterReflection->expects($this->once())
            ->method('getClass')
            ->will(
                $this->returnCallback(
                    function () {
                        throw new \ReflectionException('Some message');
                    }
                )
            );

        $this->injectable->getWrongDependencies($this->fileReflection);
    }
}
