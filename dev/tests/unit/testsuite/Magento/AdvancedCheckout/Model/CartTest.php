<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdvancedCheckout\Model;

use Magento\AdvancedCheckout\Helper\Data;

class CartTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\AdvancedCheckout\Model\Cart
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $localeFormatMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $prodFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $stockItemFactoryMock;

    protected function setUp()
    {
        $cartMock = $this->getMock('Magento\Checkout\Model\Cart', array(), array(), '', false);
        $messageFactoryMock = $this->getMock('Magento\Framework\Message\Factory', array(), array(), '', false);
        $eventManagerMock = $this->getMock('Magento\Framework\Event\ManagerInterface');
        $this->helperMock = $this->getMock('Magento\AdvancedCheckout\Helper\Data', array(), array(), '', false);
        $wishListFactoryMock = $this->getMock('Magento\Wishlist\Model\WishlistFactory', array(), array(), '', false);
        $quoteFactoryMock =  $this->getMock('Magento\Sales\Model\QuoteFactory', array(), array(), '', false);
        $this->storeManagerMock =  $this->getMock('Magento\Framework\StoreManagerInterface');
        $this->localeFormatMock =  $this->getMock('Magento\Framework\Locale\FormatInterface');
        $messageManagerMock =  $this->getMock('Magento\Framework\Message\ManagerInterface');
        $customerSessionMock =  $this->getMock('Magento\Customer\Model\Session', array(), array(), '', false);

        $this->prodFactoryMock = $this->getMock('Magento\Catalog\Model\ProductFactory', ['create'], [], '', false);
        $optionFactoryMock = $this->getMock('Magento\Catalog\Model\Product\OptionFactory', [], [], '', false);
        $prodTypesConfigMock = $this->getMock('Magento\Catalog\Model\ProductTypes\ConfigInterface', [], [], '', false);
        $cartConfigMock =  $this->getMock('Magento\Catalog\Model\Product\CartConfiguration', [], [], '', false);

        $this->itemServiceMock = $this->getMock(
            'Magento\CatalogInventory\Service\V1\StockItemService',
            [],
            [],
            '',
            false
        );

        $this->model = new \Magento\AdvancedCheckout\Model\Cart(
            $cartMock,
            $messageFactoryMock,
            $eventManagerMock,
            $this->helperMock,
            $optionFactoryMock,
            $wishListFactoryMock,
            $this->prodFactoryMock,
            $quoteFactoryMock,
            $this->storeManagerMock,
            $this->localeFormatMock,
            $messageManagerMock,
            $prodTypesConfigMock,
            $cartConfigMock,
            $customerSessionMock,
            $this->itemServiceMock
        );
    }

    /**
     * @param string $sku
     * @param array $config
     * @param array $expectedResult
     *
     * @covers \Magento\AdvancedCheckout\Model\Cart::__construct
     * @covers \Magento\AdvancedCheckout\Model\Cart::setAffectedItemConfig
     * @covers \Magento\AdvancedCheckout\Model\Cart::getAffectedItemConfig
     * @dataProvider setAffectedItemConfigDataProvider
     */
    public function testSetAffectedItemConfig($sku, $config, $expectedResult)
    {
        $this->model->setAffectedItemConfig($sku, $config);
        $this->assertEquals($expectedResult, $this->model->getAffectedItemConfig($sku));
    }

    /**
     * @return array
     */
    public function setAffectedItemConfigDataProvider()
    {
        return array(
            array(
                'sku' => 123,
                'config' => array('1'),
                'expectedResult' => array(1)
            ),
            array(
                'sku' => 0,
                'config' => array('1'),
                'expectedResult' => array(1)
            ),
            array(
                'sku' => 'aaa',
                'config' => array('1'),
                'expectedResult' => array(1)
            ),
            array(
                'sku' => '',
                'config' => array('1'),
                'expectedResult' => array()
            ),
            array(
                'sku' => false,
                'config' => array('1'),
                'expectedResult' => array(1)
            ),
            array(
                'sku' => null,
                'config' => array('1'),
                'expectedResult' => array(1)
            ),
            array(
                'sku' => 'aaa',
                'config' => array(),
                'expectedResult' => array()
            ),
            array(
                'sku' => 'aaa',
                'config' => null,
                'expectedResult' => array()
            ),
            array(
                'sku' => 'aaa',
                'config' => false,
                'expectedResult' => array()
            ),
            array(
                'sku' => 'aaa',
                'config' => 0,
                'expectedResult' => array()
            ),
            array(
                'sku' => 'aaa',
                'config' => '',
                'expectedResult' => array()
            )
        );
    }

    /**
     * @param string $sku
     * @param integer $qty
     * @param string $expectedCode
     *
     * @dataProvider prepareAddProductsBySkuDataProvider
     * @covers \Magento\AdvancedCheckout\Model\Cart::_getValidatedItem
     * @covers \Magento\AdvancedCheckout\Model\Cart::_loadProductBySku
     * @covers \Magento\AdvancedCheckout\Model\Cart::checkItem
     */
    public function testGetValidatedItem($sku, $qty, $expectedCode)
    {
        $storeMock = $this->getMock('\Magento\Core\Model\Store', array('getId', 'getWebsiteId'), array(), '', false);
        $storeMock->expects($this->any())->method('getStore')->will($this->returnValue(1));
        $storeMock->expects($this->any())->method('getWebsiteId')->will($this->returnValue(1));

        $sessionMock = $this->getMock(
            'Magento\Framework\Session\SessionManager',
            array('getAffectedItems', 'setAffectedItems'),
            array(),
            '',
            false
        );
        $sessionMock->expects($this->any())->method('getAffectedItems')->will($this->returnValue(array()));

        $productMock = $this->getMock(
            'Magento\Catalog\Model\Product',
            array('setStore', 'loadByAttribute', 'getId', 'getWebsiteIds', 'isComposite', '__wakeup', '__sleep'),
            array(),
            '',
            false
        );
        $productMock->expects($this->any())->method('setStore')->will($this->returnValue($productMock));
        $productMock->expects($this->any())->method('loadByAttribute')->will($this->returnValue($productMock));
        $productMock->expects($this->any())->method('getId')->will($this->returnValue(1));
        $productMock->expects($this->any())->method('getWebsiteIds')->will($this->returnValue(array(1)));
        $productMock->expects($this->any())->method('isComposite')->will($this->returnValue(false));
        $this->prodFactoryMock->expects($this->any())->method('create')->will($this->returnValue($productMock));
        $this->helperMock->expects($this->any())->method('getSession')->will($this->returnValue($sessionMock));
        $this->localeFormatMock->expects($this->any())->method('getNumber')->will($this->returnArgument(0));
        $this->storeManagerMock->expects($this->any())->method('getStore')->will($this->returnValue($storeMock));
        $item = $this->model->checkItem($sku, $qty);

        $this->assertTrue($item['code'] == $expectedCode);
    }

    /**
     * @return array
     */
    public function prepareAddProductsBySkuDataProvider()
    {
        return array(
            array(
                'sku' => 'aaa',
                'qty' => 2,
                'expectedCode' => \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_SUCCESS,
            ),
            array(
                'sku' => 'aaa',
                'qty' => 'aaa',
                'expectedCode' => \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_QTY_INVALID_NUMBER,
            ),
            array(
                'sku' => 'aaa',
                'qty' => -1,
                'expectedCode' =>
                    \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_QTY_INVALID_NON_POSITIVE,
            ),
            array(
                'sku' => 'aaa',
                'qty' => 0.00001,
                'expectedCode' => \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_QTY_INVALID_RANGE,
            ),
            array(
                'sku' => 'aaa',
                'qty' => 100000000.0,
                'expectedCode' => \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_QTY_INVALID_RANGE,
            ),
            array(
                'sku' => 'a',
                'qty' => 2,
                'expectedCode' => \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_SUCCESS,
            ),
            array(
                'sku' => 123,
                'qty' => 2,
                'expectedCode' => \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_SUCCESS,
            ),
            array(
                'sku' => 0,
                'qty' => 2,
                'expectedCode' => \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_SUCCESS,
            ),
            array(
                'sku' => '',
                'qty' => 2,
                'expectedCode' => \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_EMPTY,
            )
        );
    }

    /**
     * @param array $config
     * @param array $result
     * @dataProvider getQtyStatusDataProvider
     */
    public function testGetQtyStatus($config, $result)
    {
        $productId = $config['product_id'];
        $requestQty = $config['request_qty'];
        $product = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $product->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($productId));
        $resultObject = new \Magento\Framework\Object($config['result']);
        $this->itemServiceMock->expects($this->once())
            ->method('checkQuoteItemQty')
            ->with($this->equalTo($productId), $this->equalTo($requestQty), $this->equalTo($requestQty))
            ->will($this->returnValue($resultObject));

        if ($config['result']['has_error']) {
            switch ($resultObject->getErrorCode()) {
                case 'qty_increments':
                    $this->itemServiceMock->expects($this->once())
                        ->method('getQtyIncrements')
                        ->with($this->equalTo($productId))
                        ->will($this->returnValue($config['result']['qty_increments']));
                    break;
                case 'qty_min':
                    $this->itemServiceMock->expects($this->once())
                        ->method('getMinSaleQty')
                        ->with($this->equalTo($productId))
                        ->will($this->returnValue($config['result']['qty_min_allowed']));
                    break;
                case 'qty_max':
                    $this->itemServiceMock->expects($this->once())
                        ->method('getMaxSaleQty')
                        ->with($this->equalTo($productId))
                        ->will($this->returnValue($config['result']['qty_max_allowed']));
                    break;
                default:
                    $this->itemServiceMock->expects($this->once())
                        ->method('getStockQty')
                        ->with($this->equalTo($productId))
                        ->will($this->returnValue($config['result']['qty_max_allowed']));
                    break;
            }
        }
        $this->assertSame($result, $this->model->getQtyStatus($product, $requestQty));
    }

    /**
     * @return array
     */
    public function getQtyStatusDataProvider()
    {
        return [
            'error qty_increments' => [
                [
                    'product_id' => 11,
                    'request_qty' => 6,
                    'result' => [
                        'has_error' => true,
                        'error_code' => 'qty_increments',
                        'qty_increments' => 1,
                        'message' => 'hello qty_increments'
                    ]
                ],
                [
                    'qty_increments' => 1,
                    'status' => Data::ADD_ITEM_STATUS_FAILED_QTY_INCREMENTS,
                    'error' => 'hello qty_increments'
                ]
            ],
            'error qty_min' => [
                [
                    'product_id' => 14,
                    'request_qty' => 5,
                    'result' => [
                        'has_error' => true,
                        'error_code' => 'qty_min',
                        'qty_min_allowed' => 2,
                        'message' => 'hello qty_min_allowed'
                    ]
                ],
                [
                    'qty_min_allowed' => 2,
                    'status' => Data::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED_IN_CART,
                    'error' => 'hello qty_min_allowed'
                ]
            ],
            'error qty_max' => [
                [
                    'product_id' => 13,
                    'request_qty' => 4,
                    'result' => [
                        'has_error' => true,
                        'error_code' => 'qty_max',
                        'qty_max_allowed' => 3,
                        'message' => 'hello qty_max_allowed'
                    ]
                ],
                [
                    'qty_max_allowed' => 3,
                    'status' => Data::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED_IN_CART,
                    'error' => 'hello qty_max_allowed'
                ]
            ],
            'error default' => [
                [
                    'product_id' => 12,
                    'request_qty' => 3,
                    'result' => [
                        'has_error' => true,
                        'error_code' => 'default',
                        'qty_max_allowed' => 4,
                        'message' => 'hello default'
                    ]
                ],
                [
                    'qty_max_allowed' => 4,
                    'status' => Data::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED,
                    'error' => 'hello default'
                ]
            ],
            'no error' => [
                [
                    'product_id' => 18,
                    'request_qty' => 22,
                    'result' => ['has_error' => false]
                ],
                true
            ],
        ];
    }
}
