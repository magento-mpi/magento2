<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Pricing\Price;

use \Magento\Pricing\PriceInfoInterface;

/**
 * Class OptionPriceTest
 */
class OptionPriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Pricing\Price\OptionPrice
     */
    protected $object;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $product;

    /**
     * @var \Magento\Pricing\PriceInfo\Base|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceInfo;

    protected function setUp()
    {
        $this->product = $this->getMock(
            'Magento\Catalog\Model\Product',
            ['getOptionById', '__wakeup', 'getPriceInfo', 'getOptions'],
            [],
            '',
            false
        );

        $this->priceInfo = $this->getMock(
            'Magento\Pricing\PriceInfo\Base',
            [],
            [],
            '',
            false
        );

        $this->product->expects($this->any())
            ->method('getPriceInfo')
            ->will($this->returnValue($this->priceInfo));

        $this->object = new OptionPrice($this->product, PriceInfoInterface::PRODUCT_QUANTITY_DEFAULT);
    }

    /**
     * Return value
     */
    public function testGetValue()
    {
        $optionId = 1;
        $optionValue = 10;
        $optionType = 'select';
        $optionValueMock = $this->getMockBuilder('Magento\Catalog\Model\Product\Option\DefaultType')
            ->disableOriginalConstructor()
            ->setMethods(['getValue'])
            ->getMock();
        $optionMock = $this->getMockBuilder('Magento\Catalog\Model\Product\Option')
            ->disableOriginalConstructor()
            ->setMethods(['getId', 'getType', 'groupFactory', '__wakeup'])
            ->getMock();
        $groupMock = $this->getMockBuilder('Magento\Catalog\Model\Product\Option\Type\Select')
            ->disableOriginalConstructor()
            ->setMethods(['setOption', 'setConfigurationItemOption', 'getOptionPrice'])
            ->getMock();

        $groupMock->expects($this->once())
            ->method('setOption')
            ->with($this->equalTo($optionMock))
            ->will($this->returnSelf());
        $groupMock->expects($this->once())
            ->method('setConfigurationItemOption')
            ->with($this->equalTo($optionValueMock))
            ->will($this->returnSelf());
        $groupMock->expects($this->once())
            ->method('getOptionPrice')
            ->with($this->equalTo($optionValue), $this->equalTo(0.))
            ->will($this->returnValue($optionValue));
        $optionMock->expects($this->at(0))
            ->method('getId')
            ->will($this->returnValue($optionId));
        $optionMock->expects($this->once())
            ->method('getType')
            ->will($this->returnValue($optionType));
        $optionMock->expects($this->once())
            ->method('groupFactory')
            ->with($this->equalTo($optionType))
            ->will($this->returnValue($groupMock));
        $optionValueMock->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue($optionValue));
        $optionIds = new \Magento\Object(['value' => '1']);

        $customOptions = ['option_ids' => $optionIds, 'option_1' => $optionValueMock];
        $this->product->setCustomOptions($customOptions);
        $this->product->expects($this->once())
            ->method('getOptionById')
            ->with($this->equalTo($optionId))
            ->will($this->returnValue($optionMock));
        $result = $this->object->getValue();
        $this->equalTo($optionValue, $result);

        // Return from cache
        $result = $this->object->getValue();
        $this->equalTo($optionValue, $result);
    }

    /**
     * Test getOptions()
     */
    public function testGetOptions()
    {
        $price = 100;
        $displayValue = 120;
        $id = 1;
        $expected = [$id => [$price => ['base_amount' => $price, 'adjustment' => $displayValue]]];

        $amountMock = $this->getMockBuilder('Magento\Pricing\Amount')
            ->disableOriginalConstructor()
            ->getMock();
        $optionValueMock = $this->getMockBuilder('Magento\Catalog\Model\Product\Option\Value')
            ->disableOriginalConstructor()
            ->setMethods(['getPriceType', 'getPrice', 'getId', '__wakeup'])
            ->getMock();
        $optionValueMock->expects($this->once())
            ->method('getPriceType')
            ->will($this->returnValue('percent'));
        $optionValueMock->expects($this->once())
            ->method('getPrice')
            ->with($this->equalTo(true))
            ->will($this->returnValue($price));
        $optionValueMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($id));
        $optionItemMock = $this->getMockBuilder('Magento\Catalog\Model\Product\Option')
            ->disableOriginalConstructor()
            ->setMethods(['getValues', '__wakeup'])
            ->getMock();
        $optionItemMock->expects($this->once())
            ->method('getValues')
            ->will($this->returnValue(array($optionValueMock)));
        $this->priceInfo->expects($this->once())
            ->method('getAmount')
            ->with($this->equalTo($price))
            ->will($this->returnValue($amountMock));
        $amountMock->expects($this->once())
            ->method('getDisplayAmount')
            ->with($this->equalTo(null))
            ->will($this->returnValue($displayValue));
        $options = [$optionItemMock];
        $this->product->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue($options));
        $result = $this->object->getOptions();
        $this->assertEquals($expected, $result);

        // Return from cache
        $result = $this->object->getOptions();
        $this->assertEquals($expected, $result);
    }
}
