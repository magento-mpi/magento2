<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Framework
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Controller\Router\Route;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\ObjectManager
     */
    protected $objectManager;

    public function setUp()
    {
        $this->objectManager = $this->getMockBuilder('Magento\Framework\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @test
     */
    public function testCreateRoute()
    {
        $routerClass = 'router';

        $router = $this->getMockBuilder('Zend_Controller_Router_Route_Interface')
            ->setMockClassName($routerClass)
            ->getMock();

        $parameterRoute    = 'route';
        $parameterDefaults = 'defaults';
        $parameterRegs     = 'regs';
        $parameterLocale   = 'locale';

        $this->objectManager->expects($this->once())
            ->method('create')
            ->with(
                $this->logicalOr(
                    $routerClass,
                    array(
                        'route'    => $parameterRoute,
                        'defaults' => $parameterDefaults,
                        'regs'     => $parameterRegs,
                        'locale'   => $parameterLocale,
                    )
                )
            )
            ->will($this->returnValue($router));

        $object = new \Magento\Controller\Router\Route\Factory($this->objectManager);
        $expectedRouter = $object->createRoute(
            $routerClass,
            $parameterRoute,
            $parameterDefaults,
            $parameterRegs,
            $parameterLocale
        );

        $this->assertInstanceOf($routerClass, $expectedRouter);
        $this->assertInstanceOf('Zend_Controller_Router_Route_Interface', $expectedRouter);
    }

    /**
     * @test
     * @expectedException \LogicException
     */
    public function testCreateRouteNegative()
    {
        $this->objectManager->expects($this->once())
            ->method('create')
            ->will($this->returnValue(new \StdClass));

        $object = new \Magento\Controller\Router\Route\Factory($this->objectManager);
        $object->createRoute(
            'routerClass',
            'router'
        );
    }
}
