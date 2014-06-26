<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions;

class WriteServiceTest extends \PHPUnit_Framework_TestCase
{

    const PRODUCT_SKU = 'simple';

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $repositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $optionTypeBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $optionConverterMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $factoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $optionValueReaderMock;

    /**
     * @var \Magento\Catalog\Service\V1\Product\CustomOptions\WriteService
     */
    protected $writeService;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $optionBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $optionMock;

    protected function setUp()
    {
        $this->optionTypeBuilderMock = $this->getMock(
            'Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue\Reader',
            [],
            [],
            '',
            false
        );

        $this->repositoryMock = $this->getMock('\Magento\Catalog\Model\ProductRepository',
            [],
            [],
            '',
            false
        );
        $methods = [
            'getOptions',
            'getOptionById',
            'setProductOptions',
            'setHasOptions',
            'save',
            'load',
            'reset',
            'getId',
            '__wakeup',
            'setCanSaveCustomOptions'
        ];
        $this->productMock = $this->getMock('Magento\Catalog\Model\Product', $methods, [], '', false);

        $this->optionConverterMock =
            $this->getMock('Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Converter', [], [], '', false);

        $this->repositoryMock
            ->expects($this->once())
            ->method('get')
            ->with(self::PRODUCT_SKU)
            ->will($this->returnValue($this->productMock));

        $this->optionBuilderMock =
            $this->getMock('Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionBuilder', [], [], '', false);

        $this->optionValueReaderMock = $this->getMock(
            '\Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue\ReaderInterface'
        );

        $this->factoryMock = $this->getMock(
            '\Magento\Catalog\Model\Product\OptionFactory', ['create'], [], '', false, false
        );

        $this->optionMock = $this->getMock(
            '\Magento\Catalog\Model\Product\Option',
            ['__sleep', '__wakeup', 'getId', 'getTitle', 'getType', 'delete', 'getIsRequire', 'getSortOrder', 'load'],
            [],
            '',
            false,
            false
        );

        $this->factoryMock->expects($this->any())->method('create')->will($this->returnValue($this->optionMock));

        $this->writeService = new \Magento\Catalog\Service\V1\Product\CustomOptions\WriteService(
            $this->optionBuilderMock,
            $this->optionConverterMock,
            $this->repositoryMock,
            $this->optionValueReaderMock,
            $this->factoryMock
        );
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with optionId = 10
     */
    public function testRemoveFromProductWithoutOptions()
    {
        $this->optionMock->expects($this->once())->method('load')->with(10);
        $this->productMock->expects($this->once())->method('getOptions')->will($this->returnValue(array()));
        $this->productMock->expects($this->never())->method('getOptionById');
        $this->writeService->remove(self::PRODUCT_SKU, 10);
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with optionId = 10
     */
    public function testRemoveNotExistingOption()
    {
        $options[1] = [
            Data\Option::OPTION_ID => 10,
            Data\Option::TITLE => 'Some title',
            Data\Option::TYPE => 'text',
            Data\Option::IS_REQUIRE => true,
            Data\Option::SORT_ORDER => 10,
        ];
        $this->productMock->expects($this->once())->method('getOptions')->will($this->returnValue($options));
        $this->optionMock->expects($this->never())->method('delete');
        $this->writeService->remove(self::PRODUCT_SKU, 10);
    }

    public function testSuccessRemove()
    {
        $this->optionMock->expects($this->once())->method('load')->with(10);
        $this->optionMock->expects($this->any())->method('getId')->will($this->returnValue(10));

        $this->productMock
            ->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue([10 => $this->optionMock]));

        $this->optionMock->expects($this->once())->method('delete');
        $this->productMock->expects($this->once())->method('setHasOptions')->with(false);
        $this->productMock->expects($this->once())->method('save');

        $this->assertTrue($this->writeService->remove(self::PRODUCT_SKU, 10));
    }

    /**
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     */
    public function testCanNotRemove()
    {
        $this->optionMock->expects($this->once())->method('load')->with(10);
        $this->optionMock->expects($this->any())->method('getId')->will($this->returnValue(10));

        $this->productMock
            ->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue([10 => $this->optionMock]));

        $this->optionMock->expects($this->once())->method('delete');
        $this->productMock->expects($this->once())->method('setHasOptions')->with(false);
        $this->productMock->expects($this->once())->method('save')->will($this->throwException(new \Exception()));
        $this->writeService->remove(self::PRODUCT_SKU, 10);
    }

    public function testAdd()
    {
        $optionData = $this->getMock('Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option', [], [], '', false);
        $convertedOptions =  [
            Data\Option::OPTION_ID => null,
            Data\Option::TITLE => 'Some title',
            Data\Option::TYPE => 'text',
            Data\Option::IS_REQUIRE => true,
            Data\Option::SORT_ORDER => 10,
            'price_type' => 'fixed',
            'sku' => 'sku1',
            'max_characters' => 10
        ];
        $this->optionConverterMock
            ->expects($this->once())
            ->method('convert')
            ->with($optionData)
            ->will($this->returnValue($convertedOptions));

        $existingOptions = [1 => null, 2 => null];
        $currentOptions = [1 => null, 2 => null, 10 => $this->optionMock];

        $this->productMock->expects($this->at(2))
            ->method('getOptions')->will($this->returnValue($existingOptions));
        $this->productMock->expects($this->at(7))
            ->method('getOptions')->will($this->returnValue($currentOptions));

        $this->productMock->expects($this->once())->method('getId')->will($this->returnValue(1));
        $this->productMock->expects($this->once())->method('reset');
        $this->productMock->expects($this->once())->method('load')->with(1);

        $this->productMock->expects($this->once())->method('setCanSaveCustomOptions')->with(true);
        $this->productMock->expects($this->once())->method('setProductOptions')->with([$convertedOptions]);
        $this->productMock->expects($this->once())->method('save');

        $this->optionMock->expects($this->once())->method('getId')->will($this->returnValue(10));
        $this->optionMock->expects($this->once())->method('getTitle')->will($this->returnValue('Some title'));
        $this->optionMock->expects($this->once())->method('getType')->will($this->returnValue('text'));
        $this->optionMock->expects($this->once())->method('getIsRequire')->will($this->returnValue(true));
        $this->optionMock->expects($this->once())->method('getSortOrder')->will($this->returnValue(10));

        $this->optionValueReaderMock->expects($this->once())->method('read')->will($this->returnValue('some value'));

        $expectedData = [
            Data\Option::OPTION_ID => 10,
            Data\Option::TITLE => 'Some title',
            Data\Option::TYPE => 'text',
            Data\Option::IS_REQUIRE => true,
            Data\Option::SORT_ORDER => 10,
            'value' => 'some value'
        ];

        $this->optionBuilderMock->expects($this->once())
            ->method('populateWithArray')->with($expectedData)->will($this->returnSelf());
        $this->optionBuilderMock->expects($this->once())->method('create')->will($this->returnValue($optionData));

        $this->assertEquals($optionData, $this->writeService->add(self::PRODUCT_SKU, $optionData));
    }

    /**
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     */
    public function testAddWithException()
    {
        $optionData = $this->getMock('Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option', [], [], '', false);
        $convertedOptions =  [
            Data\Option::OPTION_ID => null,
            Data\Option::TITLE => 'Some title',
            Data\Option::TYPE => 'text',
            Data\Option::IS_REQUIRE => true,
            Data\Option::SORT_ORDER => 10,
            'price_type' => 'fixed',
            'sku' => 'sku1',
            'max_characters' => 10
        ];
        $this->optionConverterMock
            ->expects($this->once())
            ->method('convert')
            ->with($optionData)
            ->will($this->returnValue($convertedOptions));

        $this->productMock->expects($this->once())->method('setCanSaveCustomOptions')->with(true);
        $this->productMock->expects($this->once())->method('setProductOptions')->with([$convertedOptions]);
        $this->productMock->expects($this->once())->method('save');

        $existingOptions = [1 => null, 2 => null];
        $currentOptions = [1 => null, 2 => null];

        $this->productMock->expects($this->at(2))
            ->method('getOptions')->will($this->returnValue($existingOptions));
        $this->productMock->expects($this->at(7))
            ->method('getOptions')->will($this->returnValue($currentOptions));

        $this->productMock->expects($this->once())->method('getId')->will($this->returnValue(1));
        $this->productMock->expects($this->once())->method('reset');
        $this->productMock->expects($this->once())->method('load')->with(1);
       $this->writeService->add(self::PRODUCT_SKU, $optionData);
    }

    /**
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     */
    public function testAddWithExceptionDuringSave()
    {
        $optionData = $this->getMock('Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option', [], [], '', false);
        $convertedOptions =  [
            Data\Option::OPTION_ID => 10,
            Data\Option::TITLE => 'Some title',
            Data\Option::TYPE => 'text',
            Data\Option::IS_REQUIRE => true,
            Data\Option::SORT_ORDER => 10,
            'price_type' => 'fixed',
            'sku' => 'sku1',
            'max_characters' => 10
        ];
        $this->optionConverterMock
            ->expects($this->once())
            ->method('convert')
            ->with($optionData)
            ->will($this->returnValue($convertedOptions));

        $this->productMock->expects($this->once())->method('setCanSaveCustomOptions')->with(true);
        $this->productMock->expects($this->once())->method('setProductOptions')->with([$convertedOptions]);
        $this->productMock->expects($this->once())->method('save')->will($this->throwException(new \Exception()));
        $this->writeService->add(self::PRODUCT_SKU, $optionData);
    }
}
