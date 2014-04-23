<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Pricing\Price;

/**
 * Class CustomOptionTest
 *
 * @package Magento\ConfigurableProduct\Pricing\Price;
 */
class AttributePriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Product\PriceModifierInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceModifier;

    /**
     * @var \Magento\Pricing\Amount\Base|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $amountMock;

    /**
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $saleableItemMock;

    /**
     * @var \Magento\Pricing\Adjustment\Calculator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $calculatorMock;

    /**
     * @var \Magento\Pricing\PriceInfoInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceInfoMock;

    /**
     * @var \Magento\ConfigurableProduct\Pricing\Price\AttributePrice|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $attribute;

    /**
     * @var \Magento\Catalog\Pricing\Price\RegularPrice|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $regularPriceMock;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \Magento\ConfigurableProduct\Model\Resource\Product\Type\Configurable\Attribute|
     * \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attributeMock;

    /**
     * Test setUp
     */
    protected function setUp()
    {
        $qty = 1;
        $this->saleableItemMock = $this->getMock(
            'Magento\Catalog\Model\Product',
            [],
            [],
            '',
            false
        );
        $this->priceInfoMock = $this->getMock('Magento\Pricing\PriceInfo\Base', [], [], '', false);
        $this->amountMock = $this->getMock('Magento\Pricing\Amount\Base', [], [], '', false);
        $this->calculatorMock = $this->getMock('Magento\Pricing\Adjustment\Calculator', [], [], '', false);
        $this->regularPriceMock = $this->getMock('Magento\Catalog\Pricing\Price\RegularPrice', [], [], '', false);
        $this->priceModifier = $this->getMock(
            'Magento\Catalog\Model\Product\PriceModifierInterface',
            [],
            [],
            '',
            false
        );
        $this->storeManagerMock = $this->getMock('Magento\Store\Model\StoreManager', ['getStore'], [], '', false);
        $this->attributeMock = $this->getMock(
            'Magento\ConfigurableProduct\Model\Resource\Product\Type\Configurable\Attribute',
            [],
            [],
            '',
            false
        );
        $this->attribute = new AttributePrice(
            $this->saleableItemMock,
            $qty,
            $this->calculatorMock,
            $this->priceModifier,
            $this->storeManagerMock
        );
    }

    public function testInstanceOf()
    {
        $qty = 100;
        $object = new AttributePrice(
            $this->saleableItemMock,
            $qty,
            $this->calculatorMock,
            $this->priceModifier,
            $this->storeManagerMock
        );
        $this->assertInstanceOf('Magento\ConfigurableProduct\Pricing\Price\AttributePrice', $object);
    }
}
