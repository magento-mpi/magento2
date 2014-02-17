<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Helper\Product\Configuration;

class PluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ConfigurableProduct\Helper\Product\Configuration\Plugin
     */
    protected $plugin;

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

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $typeInstanceMock;

    protected function setUp()
    {
        $this->itemMock = $this->getMock('Magento\Catalog\Model\Product\Configuration\Item\ItemInterface');
        $this->invocationChainMock = $this->getMock('Magento\Code\Plugin\InvocationChain', array(), array(), '', false);
        $this->productMock = $this->getMock('Magento\Catalog\Model\Product', array(), array(), '', false);
        $this->typeInstanceMock =
            $this->getMock('Magento\ConfigurableProduct\Model\Product\Type\Configurable',
                array('getSelectedAttributesInfo', '__wakeup'), array(), '', false);
        $this->itemMock->expects($this->once())->method('getProduct')->will($this->returnValue($this->productMock));
        $this->invocationChainMock
            ->expects($this->once())
            ->method('proceed')
            ->with(array($this->itemMock))
            ->will($this->returnValue(array('options')));
        $this->plugin = new \Magento\ConfigurableProduct\Helper\Product\Configuration\Plugin();
    }

    public function testAroundGetOptionsWhenProductTypeIsConfigurable()
    {
        $this->productMock
            ->expects($this->once())
            ->method('getTypeId')
            ->will($this->returnValue(\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE));
        $this->productMock
            ->expects($this->once())
            ->method('getTypeInstance')
            ->will($this->returnValue($this->typeInstanceMock));
        $this->typeInstanceMock
            ->expects($this->once())
            ->method('getSelectedAttributesInfo')
            ->with($this->productMock)
            ->will($this->returnValue(array('attributes')));
        $this->assertEquals(array('attributes', 'options'),
            $this->plugin->aroundGetOptions(array($this->itemMock), $this->invocationChainMock));
    }

    public function testAroundGetOptionsWhenProductTypeIsSimple()
    {
        $this->productMock
            ->expects($this->once())
            ->method('getTypeId')
            ->will($this->returnValue('simple'));
        $this->productMock
            ->expects($this->never())->method('getTypeInstance');
        $this->assertEquals(array('options'),
            $this->plugin->aroundGetOptions(array($this->itemMock), $this->invocationChainMock));
    }
}
