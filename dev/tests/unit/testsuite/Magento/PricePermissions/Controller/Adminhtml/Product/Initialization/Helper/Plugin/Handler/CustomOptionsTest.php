<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin\Handler;

class CustomOptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CustomOptions
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;
    
    protected function setUp()
    {
        $this->productMock = $this->getMock('\Magento\Catalog\Model\Product', array(), array(), '', false);
        $this->model = new CustomOptions();
    }

    public function testHandleProductWithoutOptions()
    {
        $this->productMock->expects($this->once())
            ->method('getData')->with('product_options')
            ->will($this->returnValue(null));

        $this->productMock->expects($this->never())->method('setData');

        $this->model->handle($this->productMock);
    }

    public function testHandleProductWithoutOriginalOptions()
    {
        $this->productMock->expects($this->once())->method('getOptions')->will($this->returnValue(array()));
        $options = array(
            'one' => array('price' => '10', 'price_type' => '20'),
            'two' => array('values' => 123),
            'three' => array(
                'values' => array(
                    array('price' => 30, 'price_type' => 40),
                    array('price' => 50, 'price_type' => 60),
                ),
            ),
        );

        $expectedData = array(
            'one' => array('price' => '0', 'price_type' => '0'),
            'two' => array('values' => 123),
            'three' => array(
                'values' => array(
                    array('price' => 0, 'price_type' => 0),
                    array('price' => 0, 'price_type' => 0),
                ),
            ),
        );

        $this->productMock->expects($this->once())
            ->method('getData')->with('product_options')
            ->will($this->returnValue($options));

        $this->productMock->expects($this->once())->method('setData')->with('product_options', $expectedData);

        $this->model->handle($this->productMock);
    }

    public function testHandleProductWithOriginalOptions()
    {
        $mockedMethodList = array('getOptionId', '__wakeup', 'getType', 'getPriceType', 'getGroupByType', 'getPrice',
            'getValues'
        );

        $optionOne = $this->getMock('\Magento\Catalog\Model\Product\Option', $mockedMethodList, array(), '', false);
        $optionTwo = $this->getMock('\Magento\Catalog\Model\Product\Option', $mockedMethodList, array(), '', false);
        $optionTwoValue = $this->getMock(
            '\Magento\Catalog\Model\Product\Option\Value',
            array('getOptionTypeId', 'getPriceType', 'getPrice', '__wakeup'),
            array(), '', false
        );

        $optionOne->expects($this->any())->method('getOptionId')->will($this->returnValue('one'));
        $optionOne->expects($this->any())->method('getType')->will($this->returnValue(2));
        $optionOne->expects($this->any())->method('getGroupByType')
            ->will($this->returnValue(\Magento\Catalog\Model\Product\Option::OPTION_GROUP_DATE));
        $optionOne->expects($this->any())->method('getPrice')->will($this->returnValue(10));
        $optionOne->expects($this->any())->method('getPriceType')->will($this->returnValue(2));
        
        $optionTwo->expects($this->any())->method('getOptionId')->will($this->returnValue('three'));
        $optionTwo->expects($this->any())->method('getType')->will($this->returnValue(3));
        $optionTwo->expects($this->any())->method('getGroupByType')
            ->will($this->returnValue(\Magento\Catalog\Model\Product\Option::OPTION_GROUP_SELECT));
        $optionTwo->expects($this->any())->method('getValues')->will($this->returnValue(array($optionTwoValue)));

        $optionTwoValue->expects($this->any())->method('getOptionTypeId')->will($this->returnValue(1));
        $optionTwoValue->expects($this->any())->method('getPrice')->will($this->returnValue(100));
        $optionTwoValue->expects($this->any())->method('getPriceType')->will($this->returnValue(2));

        $this->productMock->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue(array($optionOne, $optionTwo)));

        $options = array(
            'one' => array('price' => '10', 'price_type' => '20', 'type' => 2),
            'two' => array('values' => 123, 'type' => 10),
            'three' => array(
                'type' => 3,
                'values' => array(
                    array('price' => 30, 'price_type' => 40, 'option_type_id' => '1'),
                ),
            ),
        );

        $expectedData = array(
            'one' => array('price' => 10, 'price_type' => 2, 'type' => 2),
            'two' => array('values' => 123, 'type' => 10),
            'three' => array(
                'type' => 3,
                'values' => array(
                    array('price' => 100, 'price_type' => 2, 'option_type_id' => 1),
                ),
            ),
        );

        $this->productMock->expects($this->once())
            ->method('getData')->with('product_options')
            ->will($this->returnValue($options));

        $this->productMock->expects($this->once())->method('setData')->with('product_options', $expectedData);

        $this->model->handle($this->productMock);
    }
}
