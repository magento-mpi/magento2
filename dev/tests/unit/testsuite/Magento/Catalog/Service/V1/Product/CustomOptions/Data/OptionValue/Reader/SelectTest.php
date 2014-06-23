<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue\Reader;

use \Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue;

class SelectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Select
     */
    protected $service;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $valueBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $optionMock;

    protected function setUp()
    {
        $this->valueBuilderMock = $this->getMock(
            '\Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValueBuilder', [], [], '', false
        );
        $this->optionMock = $this->getMock(
            '\Magento\Catalog\Model\Product\Option', [], [], '', false
        );
        $this->service = new Select($this->valueBuilderMock);
    }

    public function testRead()
    {
        $valueMock = $this->getMock('\Magento\Catalog\Model\Product\Option', ['getPrice', 'getPriceType', 'getSku',
            'getTitle', 'getSortOrder', 'getId','__wakeup'], [], '', false);
        $this->optionMock->expects($this->any())->method('getValues')->will($this->returnValue(array($valueMock)));
        $valueMock->expects($this->once())->method('getPrice')->will($this->returnValue('35'));
        $valueMock->expects($this->once())->method('getPriceType')->will($this->returnValue('USD'));
        $valueMock->expects($this->once())->method('getSku')->will($this->returnValue('product_sku'));
        $valueMock->expects($this->once())->method('getTitle')->will($this->returnValue('Some Title'));
        $valueMock->expects($this->once())->method('getSortOrder')->will($this->returnValue('0'));
        $valueMock->expects($this->once())->method('getId')->will($this->returnValue('12345678'));
        $fields = [
            OptionValue::PRICE => '35',
            OptionValue::PRICE_TYPE => 'USD' ,
            OptionValue::SKU => 'product_sku',
            OptionValue::TITLE => 'Some Title',
            OptionValue::SORT_ORDER => '0',
            OptionValue::ID => '12345678'
        ];
        $this->valueBuilderMock
            ->expects($this->any())->method('populateWithArray')
            ->with($fields)
            ->will($this->returnValue($this->valueBuilderMock));
        $this->valueBuilderMock->expects($this->once())->method('create')->will($this->returnValue($fields));
        $this->assertEquals(array($fields), $this->service->read($this->optionMock));
    }
}
