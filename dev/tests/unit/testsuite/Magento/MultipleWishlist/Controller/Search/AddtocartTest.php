<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\MultipleWishlist\Controller\Search;

class AddtocartTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Addtocart
     */
    protected $model;

    /**
     * @var \Magento\Framework\App\Action\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \Magento\Framework\App\ViewInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $viewMock;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $redirectMock;

    /**
     * @var \Magento\Wishlist\Model\Wishlist|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $wishlistMock;

    /**
     * @var \Magento\Wishlist\Model\WishlistFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $wishlistFactorytMock;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSessionMock;

    /**
     * @var \Magento\Framework\View\Layout|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutMock;

    /**
     * @var \Magento\Framework\View\Element\BlockInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockMock;

    /**
     * @var \Magento\Framework\Module\Manager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $moduleManagerMock;

    /**
     * @var \Magento\Wishlist\Model\LocaleQuantityProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $quantityProcessorMock;

    /**
     * @var \Magento\Framework\ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    /**
     * @var \Magento\Checkout\Model\Cart|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $checkoutCartMock;

    /**
     * @var \Magento\Framework\App\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseMock;

    /**
     * @var \Magento\Wishlist\Model\ItemFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemFactoryMock;

    /**
     * @var \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManagerMock;

    protected function setUp()
    {
        $this->wishlistMock = $this->getMockBuilder('Magento\Wishlist\Model\Wishlist')
            ->disableOriginalConstructor()
            ->getMock();
        $this->wishlistFactorytMock = $this->getMockBuilder('Magento\Wishlist\Model\WishlistFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->wishlistFactorytMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->wishlistMock));

        $this->registryMock = $this->getMockBuilder('Magento\Framework\Registry')
            ->disableOriginalConstructor()
            ->getMock();
        $this->itemFactoryMock = $this->getMockBuilder('Magento\Wishlist\Model\ItemFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $searchFactoryMock = $this->getMockBuilder('Magento\MultipleWishlist\Model\SearchFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $strategyEmailFactoryMock = $this->getMockBuilder(
            'Magento\MultipleWishlist\Model\Search\Strategy\EmailFactory'
        )
            ->disableOriginalConstructor()
            ->getMock();
        $strategyNameFactoryMock = $this->getMockBuilder(
            'Magento\MultipleWishlist\Model\Search\Strategy\NameFactory'
        )
            ->disableOriginalConstructor()
            ->getMock();
        $checkoutSessionMock = $this->getMockBuilder('Magento\Checkout\Model\Session')
            ->disableOriginalConstructor()
            ->getMock();
        $this->checkoutCartMock = $this->getMockBuilder('Magento\Checkout\Model\Cart')
            ->disableOriginalConstructor()
            ->getMock();
        $this->customerSessionMock = $this->getMockBuilder('Magento\Customer\Model\Session')
            ->disableOriginalConstructor()
            ->getMock();
        $localeResolverMock = $this->getMockBuilder('Magento\Framework\Locale\ResolverInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->requestMock = $this->getMockBuilder('Magento\Framework\App\RequestInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->responseMock = $this->getMockBuilder('Magento\Framework\App\ResponseInterface')
            ->disableOriginalConstructor()
            ->setMethods(['setRedirect'])
            ->getMockForAbstractClass();
        $this->viewMock = $this->getMockBuilder('Magento\Framework\App\ViewInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->redirectMock = $this->getMockBuilder('Magento\Framework\App\Response\RedirectInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->layoutMock = $this->getMockBuilder('Magento\Framework\View\Layout')
            ->disableOriginalConstructor()
            ->getMock();

        $this->blockMock = $this->getMockBuilder('Magento\Framework\View\Element\BlockInterface')
            ->disableOriginalConstructor()
            ->setMethods(['toHtml', 'setRefererUrl'])
            ->getMockForAbstractClass();

        $this->moduleManagerMock = $this->getMockBuilder('Magento\Framework\Module\Manager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->quantityProcessorMock = $this->getMockBuilder('Magento\Wishlist\Model\LocaleQuantityProcessor')
            ->disableOriginalConstructor()
            ->getMock();

        $this->objectManagerMock = $this->getMockBuilder('Magento\Framework\ObjectManagerInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->messageManagerMock = $this->getMockBuilder('Magento\Framework\Message\ManagerInterface')
            ->disableOriginalConstructor()
            ->setMethods(['addSuccess'])
            ->getMockForAbstractClass();

        $this->contextMock = $this->getMockBuilder('Magento\Framework\App\Action\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $this->contextMock->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($this->requestMock));
        $this->contextMock->expects($this->any())
            ->method('getResponse')
            ->will($this->returnValue($this->responseMock));
        $this->contextMock->expects($this->any())
            ->method('getView')
            ->will($this->returnValue($this->viewMock));
        $this->contextMock->expects($this->any())
            ->method('getRedirect')
            ->will($this->returnValue($this->redirectMock));
        $this->contextMock->expects($this->any())
            ->method('getObjectManager')
            ->will($this->returnValue($this->objectManagerMock));
        $this->contextMock->expects($this->any())
            ->method('getMessageManager')
            ->will($this->returnValue($this->messageManagerMock));

        $this->model = new Addtocart(
            $this->contextMock,
            $this->registryMock,
            $this->itemFactoryMock,
            $this->wishlistFactorytMock,
            $searchFactoryMock,
            $strategyEmailFactoryMock,
            $strategyNameFactoryMock,
            $checkoutSessionMock,
            $this->checkoutCartMock,
            $this->customerSessionMock,
            $localeResolverMock,
            $this->moduleManagerMock,
            $this->quantityProcessorMock
        );
    }

    public function testExecuteWithNoSelectedAndRedirectToCart()
    {
        $this->requestMock->expects($this->at(0))
            ->method('getParam')
            ->with('qty', null)
            ->willReturn(false);
        $this->requestMock->expects($this->at(1))
            ->method('getParam')
            ->with('selected', null)
            ->willReturn(false);

        $cartHelperMock = $this->getMockBuilder('Magento\Checkout\Helper\Cart')
            ->disableOriginalConstructor()
            ->getMock();
        $cartHelperMock->expects($this->once())
            ->method('getShouldRedirectToCart')
            ->willReturn(true);
        $cartHelperMock->expects($this->once())
            ->method('getCartUrl')
            ->willReturn('cart_url');

        $this->objectManagerMock->expects($this->exactly(2))
            ->method('get')
            ->with('Magento\Checkout\Helper\Cart')
            ->willReturn($cartHelperMock);

        $salesQuoteMock = $this->getMockBuilder('Magento\Sales\Model\Quote')
            ->disableOriginalConstructor()
            ->getMock();
        $salesQuoteMock->expects($this->once())
            ->method('collectTotals')
            ->willReturnSelf();

        $this->checkoutCartMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $this->checkoutCartMock->expects($this->once())
            ->method('getQuote')
            ->willReturn($salesQuoteMock);

        $this->responseMock->expects($this->once())
            ->method('setRedirect')
            ->with('cart_url');

        $this->model->execute();
    }

    public function testExecuteWithRedirectToReferer()
    {
        $this->requestMock->expects($this->at(0))
            ->method('getParam')
            ->with('qty', null)
            ->willReturn([11 => 2]);
        $this->requestMock->expects($this->at(1))
            ->method('getParam')
            ->with('selected', null)
            ->willReturn([11 => 'on']);

        $itemMock = $this->getMockBuilder('Magento\Wishlist\Model\Item')
            ->disableOriginalConstructor()
            ->getMock();
        $this->itemFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($itemMock);
        $itemMock->expects($this->once())
            ->method('loadWithOptions')
            ->with(11)
            ->willReturnSelf();

        $this->quantityProcessorMock->expects($this->once())
            ->method('process')
            ->with(2)
            ->willReturn('2');

        $itemMock->expects($this->once())
            ->method('setQty')
            ->with('2')
            ->willReturnSelf();
        $itemMock->expects($this->once())
            ->method('addToCart')
            ->with($this->checkoutCartMock, false)
            ->willReturn(true);

        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();

        $itemMock->expects($this->once())
            ->method('getProduct')
            ->willReturn($productMock);

        $cartHelperMock = $this->getMockBuilder('Magento\Checkout\Helper\Cart')
            ->disableOriginalConstructor()
            ->getMock();

        $this->objectManagerMock->expects($this->once())
            ->method('get')
            ->with('Magento\Checkout\Helper\Cart')
            ->willReturn($cartHelperMock);

        $cartHelperMock->expects($this->once())
            ->method('getShouldRedirectToCart')
            ->willReturn(false);
        $this->redirectMock->expects($this->exactly(2))
            ->method('getRefererUrl')
            ->willReturn('referer_url');

        $productMock->expects($this->once())
            ->method('getName')
            ->willReturn('product_name');
        $this->messageManagerMock->expects($this->once())
            ->method('addSuccess')
            ->with('1 product(s) have been added to shopping cart: "product_name".')
            ->willReturnSelf();

        $salesQuoteMock = $this->getMockBuilder('Magento\Sales\Model\Quote')
            ->disableOriginalConstructor()
            ->getMock();
        $salesQuoteMock->expects($this->once())
            ->method('collectTotals')
            ->willReturnSelf();

        $this->checkoutCartMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $this->checkoutCartMock->expects($this->once())
            ->method('getQuote')
            ->willReturn($salesQuoteMock);

        $this->responseMock->expects($this->once())
            ->method('setRedirect')
            ->with('referer_url');

        $this->model->execute();
    }

    public function testExecuteWithNotSalableAndNoRedirect()
    {
        $this->requestMock->expects($this->at(0))
            ->method('getParam')
            ->with('qty', null)
            ->willReturn([22 => 2]);
        $this->requestMock->expects($this->at(1))
            ->method('getParam')
            ->with('selected', null)
            ->willReturn([22 => 'on']);

        $itemMock = $this->getMockBuilder('Magento\Wishlist\Model\Item')
            ->disableOriginalConstructor()
            ->getMock();
        $this->itemFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($itemMock);
        $itemMock->expects($this->once())
            ->method('loadWithOptions')
            ->with(22)
            ->willReturnSelf();

        $this->quantityProcessorMock->expects($this->once())
            ->method('process')
            ->with(2)
            ->willReturn('2');

        $itemMock->expects($this->once())
            ->method('setQty')
            ->with('2')
            ->willReturnSelf();
        $itemMock->expects($this->once())
            ->method('addToCart')
            ->with($this->checkoutCartMock, false)
            ->willThrowException(
                new \Magento\Framework\Model\Exception(null, \Magento\Wishlist\Model\Item::EXCEPTION_CODE_NOT_SALABLE)
            );

        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();

        $itemMock->expects($this->once())
            ->method('getProduct')
            ->willReturn($productMock);

        $cartHelperMock = $this->getMockBuilder('Magento\Checkout\Helper\Cart')
            ->disableOriginalConstructor()
            ->getMock();

        $this->objectManagerMock->expects($this->once())
            ->method('get')
            ->with('Magento\Checkout\Helper\Cart')
            ->willReturn($cartHelperMock);

        $cartHelperMock->expects($this->once())
            ->method('getShouldRedirectToCart')
            ->willReturn(false);
        $this->redirectMock->expects($this->once())
            ->method('getRefererUrl')
            ->willReturn(false);

        $productMock->expects($this->once())
            ->method('getName')
            ->willReturn('product_name');
        $this->messageManagerMock->expects($this->once())
            ->method('addError')
            ->with('Cannot add the following product(s) to shopping cart: "product_name".')
            ->willReturnSelf();

        $salesQuoteMock = $this->getMockBuilder('Magento\Sales\Model\Quote')
            ->disableOriginalConstructor()
            ->getMock();
        $salesQuoteMock->expects($this->once())
            ->method('collectTotals')
            ->willReturnSelf();

        $this->checkoutCartMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $this->checkoutCartMock->expects($this->once())
            ->method('getQuote')
            ->willReturn($salesQuoteMock);

        $this->responseMock->expects($this->once())
            ->method('setRedirect')
            ->with('');

        $this->model->execute();
    }

    public function testExecuteWithHasOptions()
    {
        $this->requestMock->expects($this->at(0))
            ->method('getParam')
            ->with('qty', null)
            ->willReturn([22 => 2]);
        $this->requestMock->expects($this->at(1))
            ->method('getParam')
            ->with('selected', null)
            ->willReturn([22 => 'on']);

        $itemMock = $this->getMockBuilder('Magento\Wishlist\Model\Item')
            ->disableOriginalConstructor()
            ->getMock();
        $this->itemFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($itemMock);
        $itemMock->expects($this->once())
            ->method('loadWithOptions')
            ->with(22)
            ->willReturnSelf();

        $this->quantityProcessorMock->expects($this->once())
            ->method('process')
            ->with(2)
            ->willReturn('2');

        $itemMock->expects($this->once())
            ->method('setQty')
            ->with('2')
            ->willReturnSelf();
        $itemMock->expects($this->once())
            ->method('addToCart')
            ->with($this->checkoutCartMock, false)
            ->willThrowException(
                new \Magento\Framework\Model\Exception(
                    null,
                    \Magento\Wishlist\Model\Item::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS
                )
            );

        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();

        $itemMock->expects($this->once())
            ->method('getProduct')
            ->willReturn($productMock);

        $cartHelperMock = $this->getMockBuilder('Magento\Checkout\Helper\Cart')
            ->disableOriginalConstructor()
            ->getMock();

        $this->objectManagerMock->expects($this->once())
            ->method('get')
            ->with('Magento\Checkout\Helper\Cart')
            ->willReturn($cartHelperMock);

        $cartHelperMock->expects($this->once())
            ->method('getShouldRedirectToCart')
            ->willReturn(false);
        $this->redirectMock->expects($this->once())
            ->method('getRefererUrl')
            ->willReturn(false);

        $productMock->expects($this->once())
            ->method('getName')
            ->willReturn('product_name');
        $itemMock->expects($this->once())
            ->method('getProductUrl')
            ->willReturn('product_url');

        $salesQuoteMock = $this->getMockBuilder('Magento\Sales\Model\Quote')
            ->disableOriginalConstructor()
            ->getMock();
        $salesQuoteMock->expects($this->once())
            ->method('collectTotals')
            ->willReturnSelf();

        $this->checkoutCartMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $this->checkoutCartMock->expects($this->once())
            ->method('getQuote')
            ->willReturn($salesQuoteMock);

        $this->responseMock->expects($this->once())
            ->method('setRedirect')
            ->with('product_url');

        $this->model->execute();
    }

    public function testExecuteWithMagentoException()
    {
        $this->requestMock->expects($this->at(0))
            ->method('getParam')
            ->with('qty', null)
            ->willReturn([22 => 2]);
        $this->requestMock->expects($this->at(1))
            ->method('getParam')
            ->with('selected', null)
            ->willReturn([22 => 'on']);

        $itemMock = $this->getMockBuilder('Magento\Wishlist\Model\Item')
            ->disableOriginalConstructor()
            ->getMock();
        $this->itemFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($itemMock);
        $itemMock->expects($this->once())
            ->method('loadWithOptions')
            ->with(22)
            ->willReturnSelf();

        $this->quantityProcessorMock->expects($this->once())
            ->method('process')
            ->with(2)
            ->willReturn('2');

        $itemMock->expects($this->once())
            ->method('setQty')
            ->with('2')
            ->willReturnSelf();
        $itemMock->expects($this->once())
            ->method('addToCart')
            ->with($this->checkoutCartMock, false)
            ->willThrowException(new \Magento\Framework\Model\Exception('Unknown Magento error'));

        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();

        $itemMock->expects($this->once())
            ->method('getProduct')
            ->willReturn($productMock);

        $cartHelperMock = $this->getMockBuilder('Magento\Checkout\Helper\Cart')
            ->disableOriginalConstructor()
            ->getMock();

        $this->objectManagerMock->expects($this->once())
            ->method('get')
            ->with('Magento\Checkout\Helper\Cart')
            ->willReturn($cartHelperMock);

        $cartHelperMock->expects($this->once())
            ->method('getShouldRedirectToCart')
            ->willReturn(false);
        $this->redirectMock->expects($this->exactly(2))
            ->method('getRefererUrl')
            ->willReturn('referer_url');

        $productMock->expects($this->once())
            ->method('getName')
            ->willReturn('product_name');
        $this->messageManagerMock->expects($this->once())
            ->method('addError')
            ->with('Unknown Magento error for "product_name"')
            ->willReturnSelf();

        $salesQuoteMock = $this->getMockBuilder('Magento\Sales\Model\Quote')
            ->disableOriginalConstructor()
            ->getMock();
        $salesQuoteMock->expects($this->once())
            ->method('collectTotals')
            ->willReturnSelf();

        $this->checkoutCartMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $this->checkoutCartMock->expects($this->once())
            ->method('getQuote')
            ->willReturn($salesQuoteMock);

        $this->responseMock->expects($this->once())
            ->method('setRedirect')
            ->with('referer_url');

        $this->model->execute();
    }

    public function testExecuteWithException()
    {
        $this->requestMock->expects($this->at(0))
            ->method('getParam')
            ->with('qty', null)
            ->willReturn([22 => 2]);
        $this->requestMock->expects($this->at(1))
            ->method('getParam')
            ->with('selected', null)
            ->willReturn([22 => 'on']);

        $itemMock = $this->getMockBuilder('Magento\Wishlist\Model\Item')
            ->disableOriginalConstructor()
            ->getMock();
        $this->itemFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($itemMock);
        $itemMock->expects($this->once())
            ->method('loadWithOptions')
            ->with(22)
            ->willReturnSelf();

        $this->quantityProcessorMock->expects($this->once())
            ->method('process')
            ->with(2)
            ->willReturn('2');

        $exception = new \Exception();

        $itemMock->expects($this->once())
            ->method('setQty')
            ->with('2')
            ->willReturnSelf();
        $itemMock->expects($this->once())
            ->method('addToCart')
            ->with($this->checkoutCartMock, false)
            ->willThrowException($exception);

        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();

        $loggerMock = $this->getMockBuilder('Magento\Framework\Logger')
            ->disableOriginalConstructor()
            ->getMock();

        $this->objectManagerMock->expects($this->at(0))
            ->method('get')
            ->with('Magento\Framework\Logger')
            ->willReturn($loggerMock);

        $loggerMock->expects($this->once())
            ->method('logException')
            ->with($exception);

        $cartHelperMock = $this->getMockBuilder('Magento\Checkout\Helper\Cart')
            ->disableOriginalConstructor()
            ->getMock();

        $this->objectManagerMock->expects($this->at(1))
            ->method('get')
            ->with('Magento\Checkout\Helper\Cart')
            ->willReturn($cartHelperMock);

        $cartHelperMock->expects($this->once())
            ->method('getShouldRedirectToCart')
            ->willReturn(false);
        $this->redirectMock->expects($this->exactly(2))
            ->method('getRefererUrl')
            ->willReturn('referer_url');

        $this->messageManagerMock->expects($this->once())
            ->method('addError')
            ->with('We could not add the item to shopping cart.')
            ->willReturnSelf();

        $salesQuoteMock = $this->getMockBuilder('Magento\Sales\Model\Quote')
            ->disableOriginalConstructor()
            ->getMock();
        $salesQuoteMock->expects($this->once())
            ->method('collectTotals')
            ->willReturnSelf();

        $this->checkoutCartMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $this->checkoutCartMock->expects($this->once())
            ->method('getQuote')
            ->willReturn($salesQuoteMock);

        $this->responseMock->expects($this->once())
            ->method('setRedirect')
            ->with('referer_url');

        $this->model->execute();
    }
}
