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
            ['setParentId', '__wakeup', 'getPriceInfo'],
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

    public function testPrepareJsonAttributes()
    {
        $options = [];
        $attributeId = 1;
        $attributeCode = 'test_attribute';
        $attributeLabel = 'Test';
        $pricingValue = 100;
        $amount = 120;

        $modifiedValue = 140;
        $modifiedAmountMock = $this->getMockBuilder('Magento\Pricing\Amount\Base')
            ->disableOriginalConstructor()
            ->getMock();
        $modifiedAmountMock->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue($modifiedValue));

        $exclude = \Magento\Weee\Pricing\Adjustment::ADJUSTMENT_CODE;
        $attributePrices = [
            [
                'pricing_value' => $pricingValue
            ]
        ];

        $productAttributeMock = $this->getMockBuilder('Magento\Catalog\Model\Entity\Attribute')
            ->disableOriginalConstructor()
            ->getMock();
        $productAttributeMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($attributeId));
        $productAttributeMock->expects($this->once())
            ->method('getAttributeCode')
            ->will($this->returnValue($attributeCode));
        $productAttributeMock->expects($this->once())
            ->method('getAttributeLabel')
            ->will($this->returnValue($attributeLabel));
        $productAttributeMock->expects($this->once())
            ->method('getPrices')
            ->will($this->returnValue($attributePrices));

        $attributeMock = $this->getMockBuilder('Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute')
            ->disableOriginalConstructor()
            ->getMock();
        $attributeMock->expects($this->once())
            ->method('getProductAttribute')
            ->will($this->returnValue($productAttributeMock));
        $configurableAttributes = [$attributeMock];

        $configurableProduct = $this->getMockBuilder('Magento\ConfigurableProduct\Model\Product\Type\Configurable')
            ->disableOriginalConstructor()
            ->getMock();
        $configurableProduct->expects($this->once())
            ->method('getConfigurableAttributes')
            ->with($this->equalTo($this->saleableItemMock))
            ->will($this->returnValue($configurableAttributes));
        $this->saleableItemMock->expects($this->once())
            ->method('getTypeInstance')
            ->will($this->returnValue($configurableProduct));
        $this->saleableItemMock->expects($this->once())
            ->method('setParentId')
            ->with($this->equalTo(true));

        $this->priceModifier->expects($this->once())
            ->method('modifyPrice')
            ->with($this->equalTo($pricingValue), $this->equalTo($this->saleableItemMock))
            ->will($this->returnValue($amount));

        $this->calculatorMock->expects($this->once())
            ->method('getAmount')
            ->with($this->equalTo($amount), $this->equalTo($this->saleableItemMock), $this->equalTo($exclude))
            ->will($this->returnValue($modifiedAmount));

        $result = $this->attribute->prepareJsonAttributes($options);
    }

    /**
     * test method testGetOptionValueModified with option is_percent = true
     */
    public function testGetOptionValueModifiedIsPercent()
    {
        $finalPriceMock = $this->getMock('Magento\Catalog\Pricing\Price\RegularPrice', [], [], '', false);
        $this->saleableItemMock->expects($this->once())
            ->method('getPriceInfo')
            ->will($this->returnValue($this->priceInfoMock));
        $this->saleableItemMock->expects($this->once())
            ->method('setParentId')
            ->with($this->equalTo(true))
            ->will($this->returnValue($this->returnSelf()));
        $this->priceInfoMock->expects($this->once())
            ->method('getPrice')
            ->with($this->equalTo(\Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE))
            ->will($this->returnValue($finalPriceMock));
        $finalPriceMock->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue(50));
        $this->priceModifier->expects($this->once())
            ->method('modifyPrice')
            ->with($this->equalTo(50), $this->equalTo($this->saleableItemMock))
            ->will($this->returnValue(55));
        $this->calculatorMock->expects($this->once())
            ->method('getAmount')
            ->with(
                $this->equalTo(55),
                $this->equalTo($this->saleableItemMock),
                $this->equalTo(\Magento\Weee\Pricing\Adjustment::ADJUSTMENT_CODE)
            )
            ->will($this->returnValue(57.55));
        $this->assertEquals(
            57.55,
            $this->attribute->getOptionValueModified(
                [
                    'is_percent' => true,
                    'pricing_value' => 100
                ]
            )
        );
    }

    /**
     * test method testGetOptionValueModified with option is_percent = false
     */
    public function testGetOptionValueModifiedIsNotPercent()
    {
        $this->saleableItemMock->expects($this->once())
            ->method('setParentId')
            ->with($this->equalTo(true))
            ->will($this->returnValue($this->returnSelf()));
        $this->priceModifier->expects($this->once())
            ->method('modifyPrice')
            ->with($this->equalTo(77.33), $this->equalTo($this->saleableItemMock))
            ->will($this->returnValue(77.67));
        $this->calculatorMock->expects($this->once())
            ->method('getAmount')
            ->with(
                $this->equalTo(77.67),
                $this->equalTo($this->saleableItemMock),
                $this->equalTo(\Magento\Weee\Pricing\Adjustment::ADJUSTMENT_CODE
                )
            )
            ->will($this->returnValue(80.99));
        $this->assertEquals(
            80.99,
            $this->attribute->getOptionValueModified(
                [
                    'is_percent' => false,
                    'pricing_value' => 77.33
                ]
            )
        );
    }
}
