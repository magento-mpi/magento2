<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdvancedCheckout\Model\Resource\Sku\Errors\Grid;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadData()
    {
        $productId = "999";
        $websiteId = "9999";
        $sku = "mysku";
        $cart = $this->getCartMock($productId, $websiteId, $sku);
        $product = $this->getProductMock();
        $status = $this->getCatalogInventoryStatusMock();
        $helper = $this->getCoreHelperMock();
        $entity = $this->getEntityFactoryMock();
        $collection = new Collection($entity, $cart, $product, $status, $helper);
        $collection->loadData();
    }

    /**
     * Return cart mock instance
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\AdvancedCheckout\Model\Cart
     */
    protected function getCartMock($productId, $storeId, $sku)
    {
        $cartMock = $this->getMockBuilder('Magento\AdvancedCheckout\Model\Cart')
            ->disableOriginalConstructor()
            ->setMethods(array('getFailedItems', 'getStore'))
            ->getMock();
        $cartMock->expects($this->any())
            ->method('getFailedItems')
            ->will(
                $this->returnValue(
                    array(
                        array("item" => array(
                            "id" => $productId, "is_qty_disabled" => "false", "sku" => $sku, "qty" => "1"),
                            "code" => "failed_configure", "orig_qty" => "7")
                    )
                )
            );
        $storeMock = $this->getStoreMock($storeId);
        $cartMock->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($storeMock));

        return $cartMock;
    }

    /**
     * Return store mock instance
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Core\Model\Store
     */
    protected function getStoreMock($websiteId)
    {
        $storeMock = $this->getMock('\Magento\Core\Model\Store', array(), array(), '', false);
        $storeMock->disableOriginalConstructor();
        $storeMock->setMethods(array('getWebsiteId'));
        $storeMock->expects($this->any())
            ->method('getWebsiteId')
            ->will($this->returnValue($websiteId));

        return $storeMock;
    }

    /**
     * Return product mock instance
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\Product
     */
    protected function getProductMock()
    {
        $productMock = $this->getMock('Magento\Catalog\Model\Product', array(), array(), '', false);
        $productMock->disableOriginalConstructor();
        $productMock->setMethods(array('getPrice'));
        $productMock->expects($this->any())
            ->method('getPrice')
            ->will($this->returnValue("1"));

        return $productMock;
    }

    /**
     * Return catalogInventoryStatus mock instance
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Customer\Model\Customer
     */
    protected function getCatalogInventoryStatusMock()
    {
        $inventoryMock = $this->getMock('Magento\CatalogInventory\Model\Stock\Status', array(), array(), '', false);
        $inventoryMock->disableOriginalConstructor();
        $inventoryMock->setMethods(array('getProductStockStatus'));
        $inventoryMock->expects($this->any())
            ->method('getProductStockStatus')
            ->will($this->returnValue(array()));

        return $inventoryMock;
    }

    /**
     * Return coreHelper mock instance
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Core\Helper\Data
     */
    protected function getCoreHelperMock()
    {
        $coreHelperMock = $this->getMockBuilder('Magento\Core\Helper\Data')
            ->disableOriginalConstructor()
            ->setMethods(array('formatPrice'))
            ->getMock();
        $coreHelperMock->expects($this->any())
            ->method('formatPrice')
            ->will($this->returnValue("1"));

        return $coreHelperMock;
    }

    /**
     * Return entityFactory mock instance
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Core\Model\EntityFactory
     */
    protected function getEntityFactoryMock()
    {
        $entityFactoryMock = $this->getMock('Magento\Core\Model\EntityFactory', array(), array(), '', false);

        return $entityFactoryMock;
    }
}
