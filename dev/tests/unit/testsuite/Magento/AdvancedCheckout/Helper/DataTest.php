<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\AdvancedCheckout\Helper;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\AdvancedCheckout\Helper\Data
     */
    protected $_helper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cart;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_checkoutSession;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_products;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_product;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_quoteItem;

    public function setUp()
    {
        $this->_cart = $this->getMockBuilder('Magento\AdvancedCheckout\Model\Cart')
            ->disableOriginalConstructor()
            ->setMethods(['getFailedItems'])
            ->getMock();
        $this->_products = $this->getMockBuilder('Magento\AdvancedCheckout\Model\Resource\Product\Collection')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $catalogConfig = $this->getMockBuilder('Magento\Catalog\Model\Config')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->_checkoutSession = $this->getMockBuilder('Magento\Checkout\Model\Session')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->_quoteItem = $this->getMockBuilder('Magento\Sales\Model\Quote\Item')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $quoteItemFactory = $this->getMockBuilder('Magento\Sales\Model\Quote\ItemFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $quoteItemFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_quoteItem));

        $this->_product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $productFactory = $this->getMockBuilder('Magento\Catalog\Model\ProductFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $productFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_product));

        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_helper = $this->_objectManager->getObject('Magento\AdvancedCheckout\Helper\Data',
            [
                'cart' => $this->_cart,
                'products' => $this->_products,
                'catalogConfig' => $catalogConfig,
                'checkoutSession' => $this->_checkoutSession,
                'quoteItemFactory' => $quoteItemFactory,
                'productFactory' => $productFactory
            ]
        );
    }

    public function testGetFailedItemsFailedSku()
    {
        $failedItems = [
            ['code' => \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_SKU]
        ];
        $this->_cart->expects($this->once())
            ->method('getFailedItems')
            ->will($this->returnValue($failedItems));

        $productsCollectionMethods = [
            'addMinimalPrice',
            'addFinalPrice',
            'addTaxPercents',
            'addAttributeToSelect',
            'addUrlRewrite'
        ];
        foreach ($productsCollectionMethods as $collectionMethod) {
            $this->_products->expects($this->any())
                ->method($collectionMethod)
                ->will($this->returnValue($this->_products));
        }

        $quote = $this->getMockBuilder('Magento\Sales\Model\Quote')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->_checkoutSession->expects($this->any())
            ->method('getQuote')
            ->will($this->returnValue($quote));

        $quoteItemMethods = [
            'setProduct' => $this->_product,
            'setQuote' => $quote,
            'addData' => [
                'code' => \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_SKU,
                'product_type' => 'undefined'
            ]
        ];
        foreach ($quoteItemMethods as $quoteMethod => $expectation) {
            $this->_quoteItem->expects($this->any())
                ->method($quoteMethod)
                ->with($expectation)
                ->will($this->returnValue($this->_quoteItem));
        }

        $this->_helper->getFailedItems(true);
    }
}
 