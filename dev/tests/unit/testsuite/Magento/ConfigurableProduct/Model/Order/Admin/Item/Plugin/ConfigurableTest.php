<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Model\Order\Admin\Item\Plugin;

class ConfigurableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ConfigurableProduct\Model\Order\Admin\Item\Plugin\Configurable
     */
    protected $configurable;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $invocationChainMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    protected function setUp()
    {
        $this->itemMock =
            $this->getMock('Magento\Sales\Model\Order\Item', array('getProductType', 'getProductOptions', '__wakeup'),
                array(), '', false);
        $this->invocationChainMock = $this->getMock('Magento\Code\Plugin\InvocationChain', array(), array(), '', false);
        $this->productFactoryMock =
            $this->getMock('Magento\Catalog\Model\ProductFactory', array('create'));
        $this->productMock = $this->getMock('Magento\Catalog\Model\Product', array(), array(), '', false);
        $this->configurable = new \Magento\ConfigurableProduct\Model\Order\Admin\Item\Plugin\Configurable(
            $this->productFactoryMock
        );
    }

    public function testAroundGetNameIfProductIsConfigurable()
    {
        $this->itemMock
            ->expects($this->once())
            ->method('getProductType')
            ->will($this->returnValue(\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE));
        $this->itemMock
            ->expects($this->once())
            ->method('getProductOptions')
            ->will($this->returnValue(array('simple_name' => 'simpleName')));
        $this->invocationChainMock->expects($this->never())->method('proceed');
        $this->assertEquals('simpleName',
            $this->configurable->aroundGetName(array($this->itemMock), $this->invocationChainMock));
    }

    public function testAroundGetNameIfProductIsSimple()
    {
        $this->itemMock
            ->expects($this->once())
            ->method('getProductType')
            ->will($this->returnValue('simple'));
        $this->itemMock
            ->expects($this->never())
            ->method('getProductOptions');
        $this->invocationChainMock->expects($this->once())->method('proceed')->with(array($this->itemMock));
        $this->configurable->aroundGetName(array($this->itemMock), $this->invocationChainMock);
    }

    public function testAroundGetSkuIfProductIsConfigurable()
    {
        $this->itemMock
            ->expects($this->once())
            ->method('getProductType')
            ->will($this->returnValue(\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE));
        $this->itemMock
            ->expects($this->once())
            ->method('getProductOptions')
            ->will($this->returnValue(array('simple_sku' => 'simpleName')));
        $this->invocationChainMock->expects($this->never())->method('proceed');
        $this->assertEquals('simpleName',
            $this->configurable->aroundGetSku(array($this->itemMock), $this->invocationChainMock)
        );
    }

    public function testAroundGetSkuIfProductIsSimple()
    {
        $this->itemMock
            ->expects($this->once())
            ->method('getProductType')
            ->will($this->returnValue('simple'));
        $this->itemMock
            ->expects($this->never())
            ->method('getProductOptions');
        $this->invocationChainMock->expects($this->once())->method('proceed')->with(array($this->itemMock));
        $this->configurable->aroundGetSku(array($this->itemMock), $this->invocationChainMock);
    }

    public function testAroundGetProductIdIfProductIsConfigurable()
    {
        $this->itemMock
            ->expects($this->once())
            ->method('getProductType')
            ->will($this->returnValue(\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE));
        $this->itemMock
            ->expects($this->once())
            ->method('getProductOptions')
            ->will($this->returnValue(array('simple_sku' => 'simpleName')));
        $this->productFactoryMock
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->productMock));
        $this->productMock
            ->expects($this->once())
            ->method('getIdBySku')
            ->with('simpleName')
            ->will($this->returnValue('id'));
        $this->invocationChainMock->expects($this->never())->method('proceed');
        $this->assertEquals('id',
            $this->configurable->aroundGetProductId(array($this->itemMock), $this->invocationChainMock)
        );
    }

    public function testAroundGetProductIdIfProductIsSimple()
    {
        $this->itemMock
            ->expects($this->once())
            ->method('getProductType')
            ->will($this->returnValue('simple'));
        $this->itemMock
            ->expects($this->never())
            ->method('getProductOptions');
        $this->invocationChainMock->expects($this->once())->method('proceed')->with(array($this->itemMock));
        $this->configurable->aroundGetProductId(array($this->itemMock), $this->invocationChainMock);
    }
}
