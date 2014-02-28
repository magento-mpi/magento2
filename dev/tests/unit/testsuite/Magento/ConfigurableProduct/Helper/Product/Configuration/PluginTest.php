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
    protected $productMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $typeInstanceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    /**
     * @var \Closure
     */
    protected $closureMock;

    protected function setUp()
    {
        $this->itemMock = $this->getMock('Magento\Catalog\Model\Product\Configuration\Item\ItemInterface');
        $this->productMock = $this->getMock('Magento\Catalog\Model\Product', array(), array(), '', false);
        $this->typeInstanceMock =
            $this->getMock('Magento\ConfigurableProduct\Model\Product\Type\Configurable',
                array('getSelectedAttributesInfo', '__wakeup'), array(), '', false);
        $this->itemMock->expects($this->once())->method('getProduct')->will($this->returnValue($this->productMock));
        $this->closureMock = function () {
            return array('options');
        };
        $this->subjectMock =
            $this->getMock('Magento\Catalog\Helper\Product\Configuration', array(), array(), '', false);
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
            $this->plugin->aroundGetOptions($this->subjectMock, $this->closureMock, $this->itemMock));
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
            $this->plugin->aroundGetOptions($this->subjectMock, $this->closureMock, $this->itemMock));
    }
}
