<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product\PriceModifier;

class CompositeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Product\PriceModifier\Composite
     */
    protected $compositeModel;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceModifierMock;

    protected function setUp()
    {
        $this->objectManagerMock = $this->getMock('Magento\ObjectManager');
        $this->productMock = $this->getMock('Magento\Catalog\Model\Product', array(), array(), '', false);
        $this->priceModifierMock = $this->getMock('Magento\Catalog\Model\Product\PriceModifierInterface');
    }

    public function testModifyPriceIfModifierExists()
    {
        $this->compositeModel = new \Magento\Catalog\Model\Product\PriceModifier\Composite(
            $this->objectManagerMock,
            array('some_class_name')
        );
        $this->objectManagerMock
            ->expects($this->once())
            ->method('get')
            ->with('some_class_name')
            ->will($this->returnValue($this->priceModifierMock));
        $this->priceModifierMock
            ->expects($this->once())
            ->method('modifyPrice')
            ->with(100, $this->productMock)
            ->will($this->returnValue(150));
        $this->assertEquals(150, $this->compositeModel->modifyPrice(100, $this->productMock));
    }

    public function testModifyPriceIfModifierNotExists()
    {
        $this->compositeModel = new \Magento\Catalog\Model\Product\PriceModifier\Composite(
            $this->objectManagerMock,
            array()
        );
        $this->objectManagerMock
            ->expects($this->never())
            ->method('get');
        $this->assertEquals(100, $this->compositeModel->modifyPrice(100, $this->productMock));
    }
}
