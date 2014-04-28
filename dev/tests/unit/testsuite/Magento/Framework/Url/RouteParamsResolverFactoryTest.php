<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Url;

use Magento\TestFramework\Helper\ObjectManager;

class RouteParamsResolverFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Framework\Url\RouteParamsResolverFactory */
    protected $object;

    /** @var \Magento\Framework\ObjectManager|\PHPUnit_Framework_MockObject_MockObject */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = $this->getMock('Magento\Framework\ObjectManager');

        $objectManager = new ObjectManager($this);
        $this->object = $objectManager->getObject(
            'Magento\Framework\Url\RouteParamsResolverFactory',
            ['objectManager' => $this->objectManager]
        );
    }

    public function testCreate()
    {
        $producedInstance = $this->getMock('Magento\Framework\Url\RouteParamsResolverInterface');
        $this->objectManager->expects($this->once())
            ->method('create')
            ->with('Magento\Framework\Url\RouteParamsResolverInterface')
            ->will($this->returnValue($producedInstance));

        $this->assertSame($producedInstance, $this->object->create([]));
    }
}
