<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin\Handler\ProductType;

class BundleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Bundle
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productTypeMock;

    protected function setUp()
    {
        $this->productMock = $this->getMock(
            '\Magento\Catalog\Model\Product',
            ['getBundleSelectionsData', 'getTypeInstance', '__wakeup', 'getTypeId', 'setData', 'getStoreId'],
            [],
            '',
            false
        );
        $this->productTypeMock = $this->getMock('\Magento\Bundle\Model\Product\Type', [], [], '', false);
        $this->productMock->expects(
            $this->any()
        )->method(
            'getTypeInstance'
        )->will(
            $this->returnValue($this->productTypeMock)
        );
        $this->model = new Bundle();
    }

    public function testHandleWithNonBundleProductType()
    {
        $this->productMock->expects($this->once())->method('getTypeId')->will($this->returnValue('some product type'));
        $this->productMock->expects($this->never())->method('getBundleSelectionsData');
        $this->model->handle($this->productMock);
    }

    public function testHandleWithoutBundleSelectionData()
    {
        $this->productMock->expects(
            $this->once()
        )->method(
            'getTypeId'
        )->will(
            $this->returnValue(\Magento\Catalog\Model\Product\Type::TYPE_BUNDLE)
        );

        $this->productMock->expects($this->once())->method('getBundleSelectionsData')->will($this->returnValue(null));

        $this->productMock->expects($this->never())->method('setData');
        $this->model->handle($this->productMock);
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testHandleWithBundleOptions()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $expected = [
            [
                ['option_id' => 10],
                ['product_id' => 20],
                [
                    'product_id' => 40,
                    'option_id' => 50,
                    'delete' => true,
                    'selection_price_type' => 0,
                    'selection_price_value' => 0
                ],
                [
                    'product_id' => 60,
                    'option_id' => 70,
                    'delete' => false,
                    'selection_price_type' => 0,
                    'selection_price_value' => 0
                ],
                [
                    'product_id' => 80,
                    'option_id' => 90,
                    'delete' => false,
                    'selection_price_type' => 777,
                    'selection_price_value' => 333
                ],
            ],
        ];

        $bundleSelectionsData = [
            [
                ['option_id' => 10],
                ['product_id' => 20],
                [
                    'product_id' => 40,
                    'option_id' => 50,
                    'delete' => true,
                    'selection_price_type' => 'selection_price_type 40',
                    'selection_price_value' => 'selection_price_value 40'
                ],
                [
                    'product_id' => 60,
                    'option_id' => 70,
                    'delete' => false,
                    'selection_price_type' => 'selection_price_type 60',
                    'selection_price_value' => 'selection_price_value 60'
                ],
                [
                    'product_id' => 80,
                    'option_id' => 90,
                    'delete' => false,
                    'selection_price_type' => 'selection_price_type 80',
                    'selection_price_value' => 'selection_price_value 80'
                ],
            ],
        ];

        /** Configuring product object mock */
        $this->productMock->expects(
            $this->once()
        )->method(
            'getTypeId'
        )->will(
            $this->returnValue(\Magento\Catalog\Model\Product\Type::TYPE_BUNDLE)
        );
        $this->productMock->expects(
            $this->once()
        )->method(
            'getBundleSelectionsData'
        )->will(
            $this->returnValue($bundleSelectionsData)
        );
        $this->productMock->expects($this->once())->method('getStoreId')->will($this->returnValue(1));

        /** Configuring product selections collection mock */
        $selectionsMock = $helper->getCollectionMock('\Magento\Bundle\Model\Resource\Selection\Collection', []);

        /** Configuring bundle options mock */
        $methods = ['getOptionId', 'getSelections', '__wakeup'];
        $optionOne = $this->getMock('\Magento\Bundle\Model\Option', $methods, [], '', false);
        $optionOne->expects($this->once())->method('getOptionId')->will($this->returnValue(1));
        $optionOne->expects($this->once())->method('getSelections')->will($this->returnValue(null));

        $selectionMock = $this->getMock(
            '\Magento\Bundle\Model\Selection',
            ['getProductId', 'getSelectionPriceType', 'getSelectionPriceValue', '__wakeup'],
            [],
            '',
            false
        );
        $selectionMock->expects($this->once())->method('getProductId')->will($this->returnValue(80));
        $selectionMock->expects($this->once())->method('getSelectionPriceType')->will($this->returnValue(777));
        $selectionMock->expects($this->once())->method('getSelectionPriceValue')->will($this->returnValue(333));
        $selections = [$selectionMock];

        $optionTwo = $this->getMock('\Magento\Bundle\Model\Option', $methods, [], '', false);
        $optionTwo->expects($this->once())->method('getOptionId')->will($this->returnValue(90));
        $optionTwo->expects($this->atLeastOnce())->method('getSelections')->will($this->returnValue($selections));

        $origBundleOptions = [$optionOne, $optionTwo];

        /** Configuring product option collection mock */
        $collectionMock = $helper->getCollectionMock('\Magento\Bundle\Model\Resource\Option\Collection', []);
        $collectionMock->expects(
            $this->once()
        )->method(
            'appendSelections'
        )->with(
            $selectionsMock
        )->will(
            $this->returnValue($origBundleOptions)
        );

        /** Configuring product type object mock */
        $this->productTypeMock->expects($this->once())->method('setStoreFilter')->with(1, $this->productMock);
        $this->productTypeMock->expects(
            $this->once()
        )->method(
            'getOptionsIds'
        )->with(
            $this->productMock
        )->will(
            $this->returnValue([1, 2])
        );
        $this->productTypeMock->expects(
            $this->once()
        )->method(
            'getOptionsCollection'
        )->with(
            $this->productMock
        )->will(
            $this->returnValue($collectionMock)
        );
        $this->productTypeMock->expects(
            $this->once()
        )->method(
            'getSelectionsCollection'
        )->with(
            [1, 2],
            $this->productMock
        )->will(
            $this->returnValue($selectionsMock)
        );

        $this->productMock->expects($this->once())->method('setData')->with('bundle_selections_data', $expected);
        $this->model->handle($this->productMock);
    }
}
