<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdvancedCheckout\Model\Resource\Sku\Errors\Grid;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadData()
    {
        $productId = '3';
        $websiteId = '1';
        $sku = 'my sku';
        $typeId = 'giftcard';

        $cart = $this->getCartMock($productId, $websiteId, $sku);
        $product = $this->getProductMock($productId, $sku, $typeId);
        $helper = $this->getCoreHelperMock();
        $entity = $this->getEntityFactoryMock();
        $stockStatusMock = $this->getMockBuilder('Magento\CatalogInventory\Api\Data\StockStatusInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $registryMock = $this->getMockBuilder('Magento\CatalogInventory\Api\StockRegistryInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $registryMock->expects($this->any())
            ->method('getStockStatus')
            ->withAnyParameters()
            ->willReturn($stockStatusMock);

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $collection = $objectManager->getObject('\Magento\AdvancedCheckout\Model\Resource\Sku\Errors\Grid\Collection',
            [
                'entityFactory' => $entity,
                'cart' => $cart,
                'productModel' => $product,
                'coreHelper' => $helper,
                'stockRegistry' => $registryMock
            ]
        );
        $collection->loadData();

        foreach ($collection->getItems() as $item) {
            $product = $item->getProduct();
            if ($item->getCode() != 'failed_sku') {
                $this->assertEquals($typeId, $product->getTypeId());
            } else {
                $this->assertEquals(null, $product->getTypeId());
            }
        }
    }

    /**
     * Return cart mock instance
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\AdvancedCheckout\Model\Cart
     */
    protected function getCartMock($productId, $storeId, $sku)
    {
        $cartMock = $this->getMockBuilder(
            'Magento\AdvancedCheckout\Model\Cart'
        )->disableOriginalConstructor()->setMethods(
            ['getFailedItems', 'getStore']
        )->getMock();
        $cartMock->expects(
            $this->any()
        )->method(
            'getFailedItems'
        )->will(
            $this->returnValue(
                [
                    [
                        "item" => ["id" => $productId, "is_qty_disabled" => "false", "sku" => $sku, "qty" => "1"],
                        "code" => "failed_configure",
                        "orig_qty" => "7",
                    ],
                    [
                        "item" => ["sku" => 'invalid', "qty" => "1"],
                        "code" => "failed_sku",
                        "orig_qty" => "1"
                    ],
                ]
            )
        );
        $storeMock = $this->getStoreMock($storeId);
        $cartMock->expects($this->any())->method('getStore')->will($this->returnValue($storeMock));

        return $cartMock;
    }

    /**
     * Return store mock instance
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Store\Model\Store
     */
    protected function getStoreMock($websiteId)
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $storeMock->expects($this->any())->method('getWebsiteId')->will($this->returnValue($websiteId));

        return $storeMock;
    }

    /**
     * Return product mock instance
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\Product
     */
    protected function getProductMock($productId, $sku, $typeId)
    {
        $adapter = $this->getMockForAbstractClass(
            'Zend_Db_Adapter_Abstract',
            [],
            '',
            false,
            true,
            true,
            ['query', 'fetchRow']
        );
        $adapter->expects($this->once())->method('fetchRow')->will($this->returnValue([
            'entity_id' => $productId,
            'entity_type_id' => '4',
            'attribute_set_id' => '4',
            'type_id' => $typeId,
            'sku' => $sku,
            'price' => '10.00',
        ]));

        $resourceMock = $this->getMockForAbstractClass(
            'Magento\Framework\Model\Resource\Db\AbstractDb',
            [],
            '',
            false,
            true,
            true,
            ['__wakeup', 'getIdFieldName', '_getReadAdapter', '_getLoadSelect']);
        $resourceMock->expects($this->once())->method('_getReadAdapter')->will($this->returnValue($adapter));

        $productMock = $this->getMock('Magento\Catalog\Model\Product',
            ['__wakeup', '_beforeLoad', '_afterLoad', '_getResource'], [], '', false);
        $productMock->setPriceCalculation(false);
        $productMock->expects($this->once())->method('_getResource')->will($this->returnValue($resourceMock));

        return $productMock;
    }

    /**
     * Return coreHelper mock instance
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Core\Helper\Data
     */
    protected function getCoreHelperMock()
    {
        $coreHelperMock = $this->getMockBuilder(
            'Magento\Core\Helper\Data'
        )->disableOriginalConstructor()->setMethods(
            ['formatPrice']
        )->getMock();
        $coreHelperMock->expects($this->any())->method('formatPrice')->will($this->returnArgument(0));

        return $coreHelperMock;
    }

    /**
     * Return entityFactory mock instance
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Core\Model\EntityFactory
     */
    protected function getEntityFactoryMock()
    {
        $entityFactoryMock = $this->getMock('Magento\Core\Model\EntityFactory', [], [], '', false);

        return $entityFactoryMock;
    }
}
