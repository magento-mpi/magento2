<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Model;

class OptionRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Bundle\Model\OptionRepository
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $typeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $optionBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $linkBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $optionResourceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $linkManagementMock;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->productRepositoryMock = $this->getMock('\Magento\Catalog\Api\ProductRepositoryInterface');
        $this->typeMock = $this->getMock('\Magento\Bundle\Model\Product\Type', [], [], '', false);
        $this->optionBuilderMock = $this->getMock(
            '\Magento\Bundle\Api\Data\OptionDataBuilder',
            ['populateWithArray', 'setOptionId', 'setTitle', 'setSku', 'setProductLinks', 'create'],
            [],
            '',
            false
        );
        $this->linkBuilderMock = $this->getMock(
            '\Magento\Bundle\Api\Data\LinkDataBuilder',
            ['populateWithArray', 'setIsDefault', 'setQty', 'setIsDefined', 'setPrice', 'setPriceType', 'create'],
            [],
            '',
            false
        );
        $this->optionResourceMock = $this->getMock(
            '\Magento\Bundle\Model\Resource\Option',
            ['delete', '__wakeup', 'save'],
            [],
            '',
            false
        );
        $this->storeManagerMock = $this->getMock('\Magento\Framework\StoreManagerInterface');
        $this->linkManagementMock = $this->getMock('\Magento\Bundle\Api\ProductLinkManagementInterface');

        $this->model = new \Magento\Bundle\Model\OptionRepository(
            $this->productRepositoryMock,
            $this->typeMock,
            $this->optionBuilderMock,
            $this->linkBuilderMock,
            $this->optionResourceMock,
            $this->storeManagerMock,
            $this->linkManagementMock
        );
    }

    /**
     * @expectedException \Magento\Webapi\Exception
     * @expectedExceptionMessage Only implemented for bundle product
     */
    public function testGetThrowsExceptionIfProductIsSimple()
    {
        $productSku = 'sku';
        $productMock = $this->getMock('\Magento\Catalog\Api\Data\ProductInterface');
        $productMock->expects($this->once())
            ->method('getTypeId')
            ->willReturn(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
        $this->productRepositoryMock->expects($this->once())
            ->method('get')
            ->with($productSku)
            ->willReturn($productMock);
        $this->model->get($productSku, 100);
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage Requested option doesn't exist
     */
    public function testGetThrowsExceptionIfOptionDoesNotExist()
    {
        $productSku = 'sku';
        $optionId = 100;
        $productMock = $this->getMock('\Magento\Catalog\Api\Data\ProductInterface');
        $productMock->expects($this->once())
            ->method('getTypeId')
            ->willReturn(\Magento\Catalog\Model\Product\Type::TYPE_BUNDLE);
        $this->productRepositoryMock->expects($this->once())
            ->method('get')
            ->with($productSku)
            ->willReturn($productMock);

        $optCollectionMock = $this->getMock('\Magento\Bundle\Model\Resource\Option\Collection', [], [], '', false);
        $this->typeMock->expects($this->once())
            ->method('getOptionsCollection')
            ->with($productMock)
            ->willReturn($optCollectionMock);
        $optCollectionMock->expects($this->once())->method('setIdFilter')->with($optionId)->willReturnSelf();

        $optionMock = $this->getMock('\Magento\Bundle\Model\Option', [], [], '', false);
        $optCollectionMock->expects($this->once())->method('getFirstItem')->willReturn($optionMock);
        $optionMock->expects($this->once())->method('getId')->willReturn(null);

        $this->model->get($productSku, $optionId);
    }

    public function testGet()
    {
        $productSku = 'sku';
        $optionId = 100;
        $optionData = ['title' => 'option title'];

        $productMock = $this->getMock(
            '\Magento\Catalog\Model\Product',
            ['getTypeId', 'getTypeInstance', 'getStoreId', 'getPriceType', '__wakeup', 'getSku'],
            [],
            '',
            false
        );
        $productMock->expects($this->once())
            ->method('getTypeId')
            ->willReturn(\Magento\Catalog\Model\Product\Type::TYPE_BUNDLE);
        $productMock->expects($this->once())->method('getSku')->willReturn($productSku);

        $this->productRepositoryMock->expects($this->once())
            ->method('get')
            ->with($productSku)
            ->willReturn($productMock);

        $optCollectionMock = $this->getMock('\Magento\Bundle\Model\Resource\Option\Collection', [], [], '', false);
        $this->typeMock->expects($this->once())
            ->method('getOptionsCollection')
            ->with($productMock)
            ->willReturn($optCollectionMock);
        $optCollectionMock->expects($this->once())->method('setIdFilter')->with($optionId)->willReturnSelf();

        $optionMock = $this->getMock('\Magento\Bundle\Model\Option', [], [], '', false);
        $optCollectionMock->expects($this->once())->method('getFirstItem')->willReturn($optionMock);
        $optionMock->expects($this->exactly(2))->method('getId')->willReturn(1);
        $optionMock->expects($this->exactly(2))->method('getTitle')->willReturn($optionData['title']);
        $optionMock->expects($this->once())->method('getData')->willReturn($optionData);

        $linkMock = $this->getMocksForProductLinks($productMock, $optionId);

        $this->optionBuilderMock->expects($this->once())
            ->method('populateWithArray')
            ->with($optionData)
            ->willReturnSelf();
        $this->optionBuilderMock->expects($this->once())->method('setOptionId')->with(1)->willReturnSelf();
        $this->optionBuilderMock->expects($this->once())
            ->method('setTitle')
            ->with($optionData['title'])
            ->willReturnSelf();
        $this->optionBuilderMock->expects($this->once())->method('setSku')->with()->willReturnSelf();
        $this->optionBuilderMock->expects($this->once())
            ->method('setProductLinks')
            ->with([$linkMock])
            ->willReturnSelf();

        $newOptionMock = $this->getMock('\Magento\Bundle\Api\Data\OptionInterface');
        $this->optionBuilderMock->expects($this->once())->method('create')->willReturn($newOptionMock);

        $this->assertEquals($newOptionMock, $this->model->get($productSku, $optionId));
    }

    public function testDelete()
    {
        $optionMock = $this->getMock('\Magento\Bundle\Model\Option', [], [], '', false);
        $this->optionResourceMock->expects($this->once())->method('delete')->with($optionMock)->willReturnSelf();
        $this->assertTrue($this->model->delete($optionMock));
    }

    /**
     * @expectedException \Magento\Framework\Exception\StateException
     * @expectedExceptionMessage Cannot delete option with id 1
     */
    public function testDeleteThrowsExceptionIfCannotDelete()
    {
        $optionMock = $this->getMock('\Magento\Bundle\Model\Option', [], [], '', false);
        $optionMock->expects($this->once())->method('getOptionId')->willReturn(1);
        $this->optionResourceMock->expects($this->once())
            ->method('delete')
            ->with($optionMock)
            ->willThrowException(new \Exception);
        $this->model->delete($optionMock);
    }

    public function testDeleteById()
    {
        $productSku = 'sku';
        $optionId = 100;
        $productMock = $this->getMock('\Magento\Catalog\Api\Data\ProductInterface');
        $productMock->expects($this->once())
            ->method('getTypeId')
            ->willReturn(\Magento\Catalog\Model\Product\Type::TYPE_BUNDLE);
        $this->productRepositoryMock->expects($this->once())
            ->method('get')
            ->with($productSku)
            ->willReturn($productMock);

        $optionMock = $this->getMock('\Magento\Bundle\Model\Option', [], [], '', false);

        $optCollectionMock = $this->getMock('\Magento\Bundle\Model\Resource\Option\Collection', [], [], '', false);
        $this->typeMock->expects($this->once())
            ->method('getOptionsCollection')
            ->with($productMock)
            ->willReturn($optCollectionMock);

        $optCollectionMock->expects($this->once())->method('setIdFilter')->with($optionId)->willReturnSelf();
        $optCollectionMock->expects($this->once())->method('getFirstItem')->willReturn($optionMock);

        $this->optionResourceMock->expects($this->once())->method('delete')->with($optionMock)->willReturnSelf();
        $this->assertTrue($this->model->deleteById($productSku, $optionId));
    }

    public function testSaveIfOptionIdIsNull()
    {
        $productId = 1;
        $storeId = 2;
        $optionId = 5;

        $storeMock = $this->getMock('\Magento\Store\Model\Store', ['getId'], [], '', false);
        $storeMock->expects($this->once())->method('getId')->willReturn($storeId);
        $this->storeManagerMock->expects($this->once())->method('getStore')->willReturn($storeMock);

        $productMock = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false);
        $productMock->expects($this->once())->method('getId')->willReturn($productId);

        $optionMock = $this->getMock(
            '\Magento\Bundle\Model\Option',
            ['setStoreId', 'setParentId', 'getProductLinks', 'getOptionId'],
            [],
            '',
            false
        );

        $linkedProductMock = $this->getMock('Magento\Bundle\Api\Data\LinkInterface');
        $optionMock->expects($this->once())->method('setStoreId')->with($storeId)->willReturnSelf();
        $optionMock->expects($this->once())->method('setParentId')->with($productId)->willReturnSelf();

        $optionIdsMap = [null, $optionId, $optionId];
        $optionMock->expects($this->any())->method('getOptionId')->willReturnCallback(function() use (&$optionIdsMap) {
            return array_shift($optionIdsMap);
        });
        $optionMock->expects($this->exactly(2))->method('getProductLinks')->willReturn([$linkedProductMock]);

        $this->optionResourceMock->expects($this->once())->method('save')->with($optionMock)->willReturnSelf();
        $this->linkManagementMock->expects($this->once())
            ->method('addChild')
            ->with($productMock, $optionId, $linkedProductMock)
            ->willReturn(1);
        $this->assertEquals($optionId, $this->model->save($productMock, $optionMock));
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage Requested option doesn't exist
     */
    public function testUpdateIfOptionDoesNotExist()
    {
        $productId = 1;
        $storeId = 2;
        $optionId = 5;

        $storeMock = $this->getMock('\Magento\Store\Model\Store', ['getId'], [], '', false);
        $storeMock->expects($this->once())->method('getId')->willReturn($storeId);
        $this->storeManagerMock->expects($this->once())->method('getStore')->willReturn($storeMock);

        $productMock = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false);
        $productMock->expects($this->once())->method('getId')->willReturn($productId);

        $optionMock = $this->getMock(
            '\Magento\Bundle\Model\Option',
            ['setStoreId', 'setParentId', 'getProductLinks', 'getOptionId'],
            [],
            '',
            false
        );

        $optionMock->expects($this->once())->method('setStoreId')->with($storeId)->willReturnSelf();
        $optionMock->expects($this->once())->method('setParentId')->with($productId)->willReturnSelf();
        $optionMock->expects($this->any())->method('getOptionId')->willReturn($optionId);

        $optCollectionMock = $this->getMock('\Magento\Bundle\Model\Resource\Option\Collection', [], [], '', false);
        $this->typeMock->expects($this->once())
            ->method('getOptionsCollection')
            ->with($productMock)
            ->willReturn($optCollectionMock);

        $existingOptionMock = $this->getMock('\Magento\Bundle\Model\Option', ['getOptionId'], [], '', false);
        $optCollectionMock->expects($this->once())->method('setIdFilter')->with($optionId)->willReturnSelf();
        $optCollectionMock->expects($this->once())->method('getFirstItem')->willReturn($existingOptionMock);
        $existingOptionMock->expects($this->once())->method('getOptionId')->willReturn(null);

        $this->assertEquals($optionId, $this->model->save($productMock, $optionMock));
    }

    public function testUpdate()
    {
        $productId = 1;
        $storeId = 2;
        $optionId = 5;
        $existingOptionId = 5;
        $existingOptionTitle = 'option_title';

        $storeMock = $this->getMock('\Magento\Store\Model\Store', ['getId'], [], '', false);
        $storeMock->expects($this->once())->method('getId')->willReturn($storeId);
        $this->storeManagerMock->expects($this->once())->method('getStore')->willReturn($storeMock);

        $productMock = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false);
        $productMock->expects($this->once())->method('getId')->willReturn($productId);
        $optionMock = $this->getMock(
            '\Magento\Bundle\Model\Option',
            [
                'setStoreId',
                'setParentId',
                'getProductLinks',
                'getOptionId',
                'setOptionId',
                'setDefaultTitle',
                'getTitle'
            ],
            [],
            '',
            false
        );
        $optionMock->expects($this->once())->method('setStoreId')->with($storeId)->willReturnSelf();
        $optionMock->expects($this->once())->method('setParentId')->with($productId)->willReturnSelf();
        $optionMock->expects($this->any())->method('getOptionId')->willReturn($optionId);

        $optCollectionMock = $this->getMock('\Magento\Bundle\Model\Resource\Option\Collection', [], [], '', false);
        $this->typeMock->expects($this->once())
            ->method('getOptionsCollection')
            ->with($productMock)
            ->willReturn($optCollectionMock);
        $existingOptionMock = $this->getMock(
            '\Magento\Bundle\Model\Option',
            ['getOptionId', 'getTitle', 'getProductLinks'],
            [],
            '',
            false
        );
        $optCollectionMock->expects($this->once())->method('setIdFilter')->with($optionId)->willReturnSelf();
        $optCollectionMock->expects($this->once())->method('getFirstItem')->willReturn($existingOptionMock);
        $existingOptionMock->expects($this->any())->method('getOptionId')->willReturn($existingOptionId);
        $existingOptionMock->expects($this->once())->method('getTitle')->willReturn($existingOptionTitle);
        $existingOptionMock->expects($this->once())->method('getProductLinks')->willReturn(null);

        $linkedProductMock = $this->getMock('\Magento\Bundle\Api\Data\LinkInterface');
        $optionMock->expects($this->once())->method('setOptionId')->with($existingOptionId)->willReturnSelf();
        $optionMock->expects($this->once())->method('setDefaultTitle')->with($existingOptionTitle)->willReturnSelf();
        $optionMock->expects($this->once())->method('getTitle')->willReturn(null);
        $optionMock->expects($this->exactly(2))->method('getProductLinks')->willReturn([$linkedProductMock]);

        $this->optionResourceMock->expects($this->once())->method('save')->with($optionMock)->willReturnSelf();
        $this->linkManagementMock->expects($this->once())
            ->method('addChild')
            ->with($productMock, $optionId, $linkedProductMock)
            ->willReturn(1);
        $this->assertEquals($optionId, $this->model->save($productMock, $optionMock));
    }

//    public function testGetList()
//    {
//        $productSku = 'sku';
//        $optionId = 100;
//        $optionData = ['title' => 'title'];
//        $productMock = $this->getMock(
//            '\Magento\Catalog\Model\Product',
//            ['getTypeId', 'getTypeInstance', 'getStoreId', 'getPriceType', '__wakeup', 'getSku'],
//            [],
//            '',
//            false
//        );
//        $productMock->expects($this->once())
//            ->method('getTypeId')
//            ->willReturn(\Magento\Catalog\Model\Product\Type::TYPE_BUNDLE);
//        $this->productRepositoryMock->expects($this->once())
//            ->method('get')
//            ->with($productSku)
//            ->willReturn($productMock);
//
//        $optionMock = $this->getMock(
//            '\Magento\Bundle\Model\Option',
//            ['getOptionId', 'getData'],
//            [],
//            '',
//            false
//        );
//        $selectionCollMock = $this->objectManager->getCollectionMock(
//            'Magento\Bundle\Model\Resource\Option\Collection',
//            [$optionMock]
//        );
//        $this->typeMock->expects($this->once())
//            ->method('getOptionsCollection')
//            ->with($productMock)
//            ->willReturn($selectionCollMock);
//
//        $optionMock->expects($this->exactly(2))->method('getOptionId')->willReturn($optionId);
//        $optionMock->expects($this->once())->method('getData')->willReturn($optionData);
//        $this->getMocksForProductLinks($productMock, $optionId);
//
//        $this->optionBuilderMock->expects($this->once())
//            ->method('populateWithArray')
//            ->with($optionData)
//            ->willReturnSelf();
//        $this->optionBuilderMock->expects($this->once())->method('setOptionId')->with($optionId)->willReturnSelf();
//        $this->optionBuilderMock->expects($this->once())->method('setTitle')->with($optionId)->willReturnSelf();
//
//        $this->model->getList($productSku);
//    }

    protected function getMocksForProductLinks(\PHPUnit_Framework_MockObject_MockObject $productMock, $optionId)
    {
        $storeId = 2;
        $priceType = 'priceType';
        $priceValue = 200;
        $selectionData = ['sku' => 'sku', 'option_id' => 'option_id'];

        $typeMock = $this->getMock('\Magento\Bundle\Model\Product\Type', [], [], '', false);

        $productMock->expects($this->once())->method('getTypeInstance')->willReturn($typeMock);
        $productMock->expects($this->once())->method('getStoreId')->willReturn($storeId);
        $productMock->expects($this->exactly(2))->method('getPriceType')->willReturn($priceType);

        $typeMock->expects($this->once())->method('setStoreFilter')->with($storeId, $productMock)->willReturnSelf();
        $selectionMock = $this->getMock(
            '\Magento\Catalog\Model\Product',
            [
                'getSelectionPriceType',
                'getSelectionPriceValue',
                '__wakeup',
                'getData',
                'getIsDefault',
                'getSelectionQty',
                'getSelectionCanChangeQty',
            ],
            [],
            '',
            false
        );

        $selectionCollMock = $this->objectManager->getCollectionMock(
            'Magento\Bundle\Model\Resource\Option\Collection',
            [$selectionMock]
        );

        $typeMock->expects($this->once())
            ->method('getSelectionsCollection')
            ->with([$optionId], $productMock)
            ->willReturn($selectionCollMock);

        $selectionMock->expects($this->once())->method('getSelectionPriceType')->willReturn($priceType);
        $selectionMock->expects($this->once())->method('getSelectionPriceValue')->willReturn($priceValue);
        $selectionMock->expects($this->once())->method('getData')->willReturn($selectionData);
        $selectionMock->expects($this->once())->method('getIsDefault')->willReturn(true);
        $selectionMock->expects($this->once())->method('getSelectionQty')->willReturn(5);
        $selectionMock->expects($this->once())->method('getSelectionCanChangeQty')->willReturn(false);

        $this->linkBuilderMock->expects($this->once())
            ->method('populateWithArray')
            ->with($selectionData)
            ->willReturnSelf();

        $linkMock = $this->getMock('\Magento\Bundle\Api\Data\LinkInterface');

        $this->linkBuilderMock->expects($this->once())->method('setIsDefault')->with(true)->willReturnSelf();
        $this->linkBuilderMock->expects($this->once())->method('setQty')->with(5)->willReturnSelf();
        $this->linkBuilderMock->expects($this->once())->method('setIsDefined')->with(false)->willReturnSelf();
        $this->linkBuilderMock->expects($this->once())->method('setPrice')->with($priceValue)->willReturnSelf();
        $this->linkBuilderMock->expects($this->once())->method('setPriceType')->with($priceType)->willReturnSelf();
        $this->linkBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($linkMock);
        return $linkMock;
    }
}
