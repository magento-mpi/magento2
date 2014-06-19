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
     * @var \Magento\Catalog\Service\V1\Product\CustomOptions\WriteService
     */
    protected $writeService;

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
        $this->writeService = new \Magento\Catalog\Service\V1\Product\CustomOptions\WriteService(
            $this->optionConverterMock,
            $this->repositoryMock,
            $this->optionTypeBuilderMock
        );
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with optionId = 10
     */
    public function testRemoveFromProductWithoutOptions()
    {
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
        $this->productMock->expects($this->once())->method('getOptionById')->with(10)->will($this->returnValue(null));
        $this->productMock->expects($this->never())->method('setCanSaveCustomOptions');
        $this->writeService->remove(self::PRODUCT_SKU, 10);
    }

    public function testSuccessRemove()
    {
        $options[10] = [
            Data\Option::OPTION_ID => 10,
            Data\Option::TITLE => 'Some title',
            Data\Option::TYPE => 'text',
            Data\Option::IS_REQUIRE => true,
            Data\Option::SORT_ORDER => 10,
            Data\Option::VALUE => ['some_value']
        ];

        $methods = array('getId', 'getTitle', 'getType', 'getIsRequire', 'getSortOrder', '__wakeup');
        $optionMock = $this->getMock('\Magento\Catalog\Model\Product\Option', $methods, [], '', false);
        $this->productMock->expects($this->once())->method('getOptions')->will($this->returnValue([$optionMock]));
        $this->productMock
            ->expects($this->once())
            ->method('getOptionById')
            ->with(10)
            ->will($this->returnValue($options[10]));
        $optionMock->expects($this->exactly(2))->method('getId')->will($this->returnValue(10));
        $optionMock->expects($this->once())->method('getTitle')->will($this->returnValue('Some title'));
        $optionMock->expects($this->once())->method('getType')->will($this->returnValue('text'));
        $optionMock->expects($this->once())->method('getIsRequire')->will($this->returnValue(true));
        $optionMock->expects($this->once())->method('getSortOrder')->will($this->returnValue(10));
        $this->optionTypeBuilderMock
            ->expects($this->once())
            ->method('read')
            ->with($optionMock)
            ->will($this->returnValue(array('some_value')));
        $options[10]['is_delete'] = '1';
        $this->productMock->expects($this->once())->method('setCanSaveCustomOptions')->with(true);
        $this->productMock->expects($this->once())->method('setProductOptions')->with($options);
        $this->productMock->expects($this->once())->method('setHasOptions')->with(true);
        $this->productMock->expects($this->once())->method('save');
        $this->assertTrue($this->writeService->remove(self::PRODUCT_SKU, 10));
    }

    /**
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     */
    public function testCanntRemove()
    {
        $options[10] = [
            Data\Option::OPTION_ID => 10,
            Data\Option::TITLE => 'Some title',
            Data\Option::TYPE => 'text',
            Data\Option::IS_REQUIRE => true,
            Data\Option::SORT_ORDER => 10,
            Data\Option::VALUE => ['some_value']
        ];

        $methods = array('getId', 'getTitle', 'getType', 'getIsRequire', 'getSortOrder', '__wakeup');
        $optionMock = $this->getMock('\Magento\Catalog\Model\Product\Option', $methods, [], '', false);
        $this->productMock->expects($this->once())->method('getOptions')->will($this->returnValue([$optionMock]));
        $this->productMock
            ->expects($this->once())
            ->method('getOptionById')
            ->with(10)
            ->will($this->returnValue($options[10]));
        $optionMock->expects($this->exactly(2))->method('getId')->will($this->returnValue(10));
        $optionMock->expects($this->once())->method('getTitle')->will($this->returnValue('Some title'));
        $optionMock->expects($this->once())->method('getType')->will($this->returnValue('text'));
        $optionMock->expects($this->once())->method('getIsRequire')->will($this->returnValue(true));
        $optionMock->expects($this->once())->method('getSortOrder')->will($this->returnValue(10));
        $this->optionTypeBuilderMock
            ->expects($this->once())
            ->method('read')
            ->with($optionMock)
            ->will($this->returnValue(array('some_value')));
        $options[10]['is_delete'] = '1';
        $this->productMock->expects($this->once())->method('setCanSaveCustomOptions')->with(true);
        $this->productMock->expects($this->once())->method('setProductOptions')->with($options);
        $this->productMock->expects($this->once())->method('setHasOptions')->with(true);
        $this->productMock->expects($this->once())->method('save')->will($this->throwException(new \Exception()));
        $this->writeService->remove(self::PRODUCT_SKU, 10);
    }
}
