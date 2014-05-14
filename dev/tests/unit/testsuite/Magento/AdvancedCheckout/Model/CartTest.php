<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdvancedCheckout\Model;

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
        $cartMock = $this->getMock('\Magento\Checkout\Model\Cart', array(), array(), '', false);
        $messageFactoryMock = $this->getMock('\Magento\Framework\Message\Factory', array(), array(), '', false);
        $eventManagerMock = $this->getMock('\Magento\Framework\Event\ManagerInterface');
        $this->helperMock = $this->getMock('\Magento\AdvancedCheckout\Helper\Data', array(), array(), '', false);
        $wishListFactoryMock = $this->getMock('\Magento\Wishlist\Model\WishlistFactory', array(), array(), '', false);
        $quoteFactoryMock =  $this->getMock('\Magento\Sales\Model\QuoteFactory', array(), array(), '', false);
        $this->storeManagerMock =  $this->getMock('\Magento\Store\Model\StoreManagerInterface');
        $this->localeFormatMock =  $this->getMock('\Magento\Framework\Locale\FormatInterface');
        $messageManagerMock =  $this->getMock('\Magento\Framework\Message\ManagerInterface');
        $customerSessionMock =  $this->getMock('\Magento\Customer\Model\Session', array(), array(), '', false);

        $this->prodFactoryMock = $this->getMock(
            '\Magento\Catalog\Model\ProductFactory', array('create'), array(), '', false
        );
        $optionFactoryMock = $this->getMock(
            '\Magento\Catalog\Model\Product\OptionFactory', array(), array(), '', false
        );
        $this->stockItemFactoryMock = $this->getMock(
            '\Magento\CatalogInventory\Model\Stock\ItemFactory', array('create'), array(), '', false
        );
        $prodTypesConfigMock =  $this->getMock(
            '\Magento\Catalog\Model\ProductTypes\ConfigInterface', array(), array(), '', false
        );
        $cartConfigMock =  $this->getMock(
            '\Magento\Catalog\Model\Product\CartConfiguration', array(), array(), '', false
        );

        $this->model = new \Magento\AdvancedCheckout\Model\Cart(
            $cartMock,
            $messageFactoryMock,
            $eventManagerMock,
            $this->helperMock,
            $optionFactoryMock,
            $this->stockItemFactoryMock,
            $wishListFactoryMock,
            $this->prodFactoryMock,
            $quoteFactoryMock,
            $this->storeManagerMock,
            $this->localeFormatMock,
            $messageManagerMock,
            $prodTypesConfigMock,
            $cartConfigMock,
            $customerSessionMock
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
            '\Magento\Framework\Session\SessionManager', array('getAffectedItems', 'setAffectedItems'),
            array(), '', false
        );
        $sessionMock->expects($this->any())->method('getAffectedItems')->will($this->returnValue(array()));

        $productMock = $this->getMock(
            '\Magento\Catalog\Model\Product',
            array('setStore', 'loadByAttribute', 'getId', 'getWebsiteIds', 'isComposite', '__wakeup', '__sleep'),
            array(), '', false
        );
        $productMock->expects($this->any())->method('setStore')->will($this->returnValue($productMock));
        $productMock->expects($this->any())->method('loadByAttribute')->will($this->returnValue($productMock));
        $productMock->expects($this->any())->method('getId')->will($this->returnValue(1));
        $productMock->expects($this->any())->method('getWebsiteIds')->will($this->returnValue(array(1)));
        $productMock->expects($this->any())->method('isComposite')->will($this->returnValue(false));

        $stockItemMock = $this->getMock('\Magento\CatalogInventory\Model\Stock\Item', array(), array(), '', false);

        $this->stockItemFactoryMock->expects($this->any())->method('create')->will($this->returnValue($stockItemMock));
        $this->prodFactoryMock->expects($this->any())->method('create')->will($this->returnValue($productMock));
        $this->helperMock->expects($this->any())->method('getSession')->will($this->returnValue($sessionMock));
        $this->localeFormatMock->expects($this->any())->method('getNumber')->will($this->returnArgument(0));
        $this->storeManagerMock->expects($this->any())->method('getStore')->will($this->returnValue($storeMock));
        $item = $this->model->checkItem($sku, $qty);

        $this->assertTrue($item['code'] == $expectedCode);
    }

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
}
