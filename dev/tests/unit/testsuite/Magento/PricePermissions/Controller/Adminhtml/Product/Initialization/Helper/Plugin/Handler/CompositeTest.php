<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin\Handler;

class CompositeTest extends \PHPUnit_Framework_TestCase
{
    public function testHandle()
    {
        $factoryMock = $this->getMock(
            '\Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin\HandlerFactory',
            array(), array(), '', false
        );

        $constructorMock = $this->getMock(
            '\Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin\HandlerInterface'
        );

        $factoryMock->expects($this->exactly(2))
            ->method('create')
            ->with('handlerInstance')
            ->will($this->returnValue($constructorMock));

        $productMock = $this->getMock('\Magento\Catalog\Model\Product', array(), array(), '', false);

        $constructorMock->expects($this->exactly(2))->method('handle')->with($productMock);

        $model = new Composite(
            $factoryMock, array('handlerInstance', 'handlerInstance')
        );

        $model->handle($productMock);
    }
}
