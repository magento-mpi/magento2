<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Model;

class ItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * GiftRegistry item instance
     *
     * @var \Magento\GiftRegistry\Model\Item
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $cartMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $catalogUrlMock;

    protected function setUp()
    {
        $contextMock = $this->getMock('\Magento\Framework\Model\Context', array(), array(), '', false);
        $registryMock = $this->getMock('\Magento\Framework\Registry', array(), array(), '', false);
        $productRepositoryMock = $this->getMock('\Magento\Catalog\Model\ProductRepository', array(), array(), '', false);
        $itemOptionMock = $this->getMock('\Magento\GiftRegistry\Model\Item\OptionFactory', array(), array(), '', false);
        $this->catalogUrlMock = $this->getMock('\Magento\Catalog\Model\Resource\Url', array(), array(), '', false);
        $this->messageManagerMock = $this->getMock('\Magento\Framework\Message\ManagerInterface');
        $this->cartMock = $this->getMock('\Magento\Checkout\Model\Cart', array(), array(), '', false);

        $resourceMock = $this->getMock(
            '\Magento\Framework\Model\Resource\AbstractResource',
            array('_construct', '_getReadAdapter', '_getWriteAdapter', 'getIdFieldName'),
            array(), '', false
        );

        $this->productMock = $this->getMock(
            '\Magento\Catalog\Model\Product',
            array(
                'getStatus', 'getName', 'getId', 'isVisibleInSiteVisibility', 'addCustomOption', 'setUrlDataObject',
                'getVisibleInSiteVisibilities', 'getUrlDataObject', 'getStoreId', 'isSalable', '__wakeup', '__sleep'
            ),
            array(), '', false
        );

        $this->model = new \Magento\GiftRegistry\Model\Item(
            $contextMock,
            $registryMock,
            $productRepositoryMock,
            $itemOptionMock,
            $this->catalogUrlMock,
            $this->messageManagerMock,
            $resourceMock
        );
    }

    /**
     * @covers \Magento\GiftRegistry\Model\Item::__construct
     * @covers \Magento\GiftRegistry\Model\Item::addToCart
     */
    public function testAddToCartProductDisabled()
    {
        $this->productMock->expects($this->once())->method('getStatus')
            ->will($this->returnValue(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED));
        $this->productMock->expects($this->never())->method('isVisibleInSiteVisibility');

        $this->cartMock->expects($this->once())->method('getProductIds')->will($this->returnValue(array()));

        $this->model->setData('product', $this->productMock);
        $this->assertEquals(false, $this->model->addToCart($this->cartMock, 1));
    }

    /**
     * @covers \Magento\GiftRegistry\Model\Item::addToCart
     */
    public function testAddToCartRequestedQuantityExceeded()
    {
        $this->productMock->expects($this->once())->method('getStatus')
            ->will($this->returnValue(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED));

        $this->productMock->expects($this->once())->method('getName')->will($this->returnValue('Product'));
        $this->cartMock->expects($this->once())->method('getProductIds')->will($this->returnValue(array()));

        $this->model->setData('product', $this->productMock);
        $this->model->setData('qty', 1);
        $this->model->setData('qty_fullfilled', 0);

        $this->messageManagerMock->expects($this->once())
            ->method('addNotice')
            ->with('The quantity of "Product" product added to cart exceeds the quantity desired by '
                . 'the Gift Registry owner. The quantity added has been adjusted to meet remaining quantity 1.');

        $this->assertEquals(false, $this->model->addToCart($this->cartMock, 5));
    }

    /**
     * @covers \Magento\GiftRegistry\Model\Item::addToCart
     */
    public function testAddToCartThatAlreadyContainsQuantityThatExceedsRequested()
    {
        $registryItemId = 17;
        $productId = 1;
        $itemProductQty = 1;
        $registryQty = 1;
        $fullFilledQty = 0;
        $requestedQty = 5;

        $quoteMock = $this->getMock('\Magento\Sales\Model\Quote', array(), array(), '', false);
        $itemMock = $this->getMock('\Magento\Sales\Model\Quote\Item\AbstractItem', array(), array(), '', false);

        $this->productMock->expects($this->once())->method('getStatus')
            ->will($this->returnValue(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED));
        $this->productMock->expects($this->any())->method('getName')->will($this->returnValue('Product'));
        $this->productMock->expects($this->any())->method('getId')->will($this->returnValue($productId));

        $this->cartMock->expects($this->once())->method('getProductIds')->will($this->returnValue(array($productId)));
        $this->cartMock->expects($this->any())->method('getQuote')->will($this->returnValue($quoteMock));

        $itemMock->expects($this->any())->method('getProduct')->will($this->returnValue($this->productMock));
        $itemMock->expects($this->any())->method('getGiftregistryItemId')->will($this->returnValue($registryItemId));
        $itemMock->expects($this->any())->method('getQty')->will($this->returnValue($itemProductQty));

        $quoteMock->expects($this->once())->method('getAllItems')->will($this->returnValue(array($itemMock)));

        $this->model->setData('product', $this->productMock);
        $this->model->setData('qty', $registryQty);
        $this->model->setData('qty_fullfilled', $fullFilledQty);
        $this->model->setData('id', $registryItemId);

        $this->messageManagerMock->expects($this->exactly(2))->method('addNotice');
        $this->assertEquals(false, $this->model->addToCart($this->cartMock, $requestedQty));
    }

    /**
     * @covers \Magento\GiftRegistry\Model\Item::addToCart
     */
    public function testAddToCartProductInvisibleForCurrentStore()
    {
        $storeId = 3;

        $this->productMock->expects($this->once())->method('getStatus')
            ->will($this->returnValue(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED));
        $this->productMock->expects($this->once())->method('isVisibleInSiteVisibility')
            ->will($this->returnValue(false));
        $this->productMock->expects($this->any())->method('getStoreId')->will($this->returnValue($storeId));
        $this->cartMock->expects($this->once())->method('getProductIds')->will($this->returnValue(array()));

        $this->model->setData('product', $this->productMock);
        $this->model->setData('store_id', $storeId);
        $this->model->setData('qty', 1);

        $this->messageManagerMock->expects($this->never())->method('addNotice');
        $this->assertEquals(false, $this->model->addToCart($this->cartMock, 1));
    }

    /**
     * @covers \Magento\GiftRegistry\Model\Item::addToCart
     */
    public function testAddToCartProductFromOtherStoreWithoutUrlRewrites()
    {
        $productStoreId = 3;
        $registryStoreId = 2;
        $productId = 1;

        $this->productMock->expects($this->once())->method('getStatus')
            ->will($this->returnValue(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED));
        $this->productMock->expects($this->once())->method('isVisibleInSiteVisibility')
            ->will($this->returnValue(false));
        $this->productMock->expects($this->any())->method('getStoreId')->will($this->returnValue($productStoreId));
        $this->productMock->expects($this->any())->method('getId')->will($this->returnValue($productId));
        $this->productMock->expects($this->never())->method('setUrlDataObject');

        $this->cartMock->expects($this->once())->method('getProductIds')->will($this->returnValue(array()));

        $this->catalogUrlMock->expects($this->once())->method('getRewriteByProductStore')
            ->with(array($productId => $registryStoreId))->will($this->returnValue(array()));

        $this->model->setData('product', $this->productMock);
        $this->model->setData('store_id', $registryStoreId);
        $this->model->setData('qty', 1);

        $this->messageManagerMock->expects($this->never())->method('addNotice');
        $this->assertEquals(false, $this->model->addToCart($this->cartMock, 1));
    }

    /**
     * @covers \Magento\GiftRegistry\Model\Item::addToCart
     */
    public function testAddToCartProductUrlIsNotVisibleInSite()
    {
        $productStoreId = 3;
        $registryStoreId = 2;
        $productId = 1;

        $objectMock = $this->getMock('\Magento\Framework\Object', array('getVisibility'), array(), '', false);
        $objectMock->expects($this->once())->method('getVisibility')->will($this->returnValue(1));

        $this->productMock->expects($this->once())->method('getStatus')
            ->will($this->returnValue(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED));
        $this->productMock->expects($this->once())->method('isVisibleInSiteVisibility')
            ->will($this->returnValue(false));
        $this->productMock->expects($this->any())->method('getStoreId')->will($this->returnValue($productStoreId));
        $this->productMock->expects($this->any())->method('getId')->will($this->returnValue($productId));
        $this->productMock->expects($this->once())->method('setUrlDataObject');
        $this->productMock->expects($this->once())->method('getUrlDataObject')->will($this->returnValue($objectMock));
        $this->productMock->expects($this->once())->method('getVisibleInSiteVisibilities')
            ->will($this->returnValue(array()));
        $this->productMock->expects($this->never())->method('isSalable');

        $this->cartMock->expects($this->once())->method('getProductIds')->will($this->returnValue(array()));

        $this->catalogUrlMock->expects($this->once())->method('getRewriteByProductStore')
            ->with(array($productId => $registryStoreId))->will($this->returnValue(array($productId => $productId)));

        $this->model->setData('product', $this->productMock);
        $this->model->setData('store_id', $registryStoreId);
        $this->model->setData('qty', 1);

        $this->messageManagerMock->expects($this->never())->method('addNotice');
        $this->assertEquals(false, $this->model->addToCart($this->cartMock, 1));
    }

    /**
     * @expectedException \Magento\Framework\Model\Exception
     * @expectedExceptionMessage This product(s) is out of stock.
     *
     * @covers \Magento\GiftRegistry\Model\Item::addToCart
     */
    public function testAddToCartProductNotSalable()
    {
        $this->productMock->expects($this->once())->method('getStatus')
            ->will($this->returnValue(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED));
        $this->productMock->expects($this->once())->method('isVisibleInSiteVisibility')
            ->will($this->returnValue(true));
        $this->productMock->expects($this->any())->method('isSalable')->will($this->returnValue(false));

        $this->cartMock->expects($this->once())->method('getProductIds')->will($this->returnValue(array()));

        $this->model->setData('product', $this->productMock);
        $this->model->setData('qty', 1);

        $this->model->addToCart($this->cartMock, 1);
    }
}
