<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Url;

use Magento\TestFramework\Helper\ObjectManager;

class RouteParamsResolverFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Url\RouteParamsResolverFactory */
    protected $object;

    /** @var \Magento\ObjectManager|\PHPUnit_Framework_MockObject_MockObject */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = $this->getMock('Magento\ObjectManager');

        $objectManager = new ObjectManager($this);
        $this->object = $objectManager->getObject(
            'Magento\Url\RouteParamsResolverFactory',
            ['objectManager' => $this->objectManager]
        );
    }

    public function testCreate()
    {
        $producedInstance = $this->getMock('Magento\Url\RouteParamsResolverInterface');
        $this->objectManager->expects($this->once())->method('create')->with('Magento\Url\RouteParamsResolverInterface')
            ->will($this->returnValue($producedInstance));

        $this->assertSame($producedInstance, $this->object->create([]));
    }
}
