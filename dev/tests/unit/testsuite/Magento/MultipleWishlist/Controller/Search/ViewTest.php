<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Controller\Search;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var View
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
     * @var \Magento\Framework\App\Response\HttpInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseMock;

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
     * @var \Magento\Wishlist\Model\ItemFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemFactoryMock;

    /**
     * @var \Magento\MultipleWishlist\Model\SearchFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $searchFactoryMock;

    /**
     * @var \Magento\MultipleWishlist\Model\Search\Strategy\EmailFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $strategyEmailFactoryMock;

    /**
     * @var \Magento\MultipleWishlist\Model\Search\Strategy\NameFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $strategyNameFactoryMock;

    /**
     * @var \Magento\Checkout\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $checkoutSessionMock;

    /**
     * @var \Magento\Checkout\Model\Cart|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $checkoutCartMock;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSessionMock;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $localeResolverMock;

    /**
     * @var \Magento\Framework\View\Layout|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutMock;

    /**
     * @var \Magento\Framework\View\Element\BlockInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockMock;

    protected function setUp()
    {
        $this->wishlistMock = $this->getMockBuilder('Magento\Wishlist\Model\Wishlist')
            ->disableOriginalConstructor()
            ->getMock();
        $this->wishlistFactorytMock = $this->getMockBuilder('Magento\Wishlist\Model\WishlistFactory')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
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
        $this->searchFactoryMock = $this->getMockBuilder('Magento\MultipleWishlist\Model\SearchFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->strategyEmailFactoryMock = $this->getMockBuilder(
            'Magento\MultipleWishlist\Model\Search\Strategy\EmailFactory'
        )
            ->disableOriginalConstructor()
            ->getMock();
        $this->strategyNameFactoryMock = $this->getMockBuilder(
            'Magento\MultipleWishlist\Model\Search\Strategy\NameFactory'
        )
            ->disableOriginalConstructor()
            ->getMock();
        $this->checkoutSessionMock = $this->getMockBuilder('Magento\Checkout\Model\Session')
            ->disableOriginalConstructor()
            ->getMock();
        $this->checkoutCartMock = $this->getMockBuilder('Magento\Checkout\Model\Cart')
            ->disableOriginalConstructor()
            ->getMock();
        $this->customerSessionMock = $this->getMockBuilder('Magento\Customer\Model\Session')
            ->disableOriginalConstructor()
            ->getMock();
        $this->localeResolverMock = $this->getMockBuilder('Magento\Framework\Locale\ResolverInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestMock = $this->getMockBuilder('Magento\Framework\App\RequestInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->responseMock = $this->getMockBuilder('Magento\Framework\App\Response\HttpInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->viewMock = $this->getMockBuilder('Magento\Framework\App\ViewInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->redirectMock = $this->getMockBuilder('Magento\Framework\App\Response\RedirectInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->layoutMock = $this->getMockBuilder('Magento\Framework\View\Layout')
            ->disableOriginalConstructor()
            ->getMock();

        $this->blockMock = $this->getMockBuilder('Magento\Framework\View\Element\BlockInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('toHtml', 'setRefererUrl'))
            ->getMock();

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

        $this->model = new View(
            $this->contextMock,
            $this->registryMock,
            $this->itemFactoryMock,
            $this->wishlistFactorytMock,
            $this->searchFactoryMock,
            $this->strategyEmailFactoryMock,
            $this->strategyNameFactoryMock,
            $this->checkoutSessionMock,
            $this->checkoutCartMock,
            $this->customerSessionMock,
            $this->localeResolverMock
        );
    }

    /**
     * @expectedException \Magento\Framework\App\Action\NotFoundException
     */
    public function testExecuteNotFoundFirst()
    {
        $this->requestMock->expects($this->at(0))
            ->method('getParam')
            ->with($this->equalTo('wishlist_id'))
            ->will($this->returnValue(false));

        $this->model->execute();
    }

    /**
     * @param $wishlistId
     * @param $visibility
     * @param $customerId
     *
     * @dataProvider getNotFoundParametersDataProvider
     * @expectedException \Magento\Framework\App\Action\NotFoundException
     */
    public function testExecuteNotFoundSecond($wishlistId, $visibility, $customerId)
    {
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with($this->equalTo('wishlist_id'))
            ->will($this->returnValue(true));

        $this->wishlistMock->expects($this->once())
            ->method('load')
            ->willReturnSelf();
        $this->wishlistMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($wishlistId));
        $this->wishlistMock->expects($this->any())
            ->method('getVisibility')
            ->will($this->returnValue($visibility));
        $this->wishlistMock->expects($this->any())
            ->method('getCustomerId')
            ->will($this->returnValue($customerId));

        $this->customerSessionMock->expects($this->any())
            ->method('getCustomerId')
            ->will($this->returnValue(1));

        $this->model->execute();
    }

    /**
     * @return array
     */
    public function getNotFoundParametersDataProvider()
    {
        return [
            [0, 0, 0],
            [1, 0, 0],
            [0, 1, 0],
        ];
    }

    public function testExecute()
    {
        $wishlistId = 1;

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with($this->equalTo('wishlist_id'))
            ->will($this->returnValue(true));

        $this->wishlistMock->expects($this->once())
            ->method('load')
            ->willReturnSelf();

        $this->wishlistMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($wishlistId));
        $this->wishlistMock->expects($this->any())
            ->method('getVisibility')
            ->will($this->returnValue(1));

        $this->registryMock->expects($this->once())
            ->method('register')
            ->with($this->equalTo('shared_wishlist'), $this->equalTo($this->wishlistMock))
            ->will($this->returnValue(1));

        $this->viewMock->expects($this->once())
            ->method('loadLayout')
            ->willReturnSelf();
        $this->viewMock->expects($this->once())
            ->method('renderLayout')
            ->willReturnSelf();
        $this->viewMock->expects($this->any())
            ->method('getLayout')
            ->willReturn($this->layoutMock);

        $this->blockMock->expects($this->once())
            ->method('setRefererUrl')
            ->willReturnMap([
                ['', $this->layoutMock]
            ]);

        $this->layoutMock->expects($this->once())
            ->method('getBlock')
            ->willReturnMap([
                ['customer.wishlist.info', $this->blockMock]
            ]);
        $this->layoutMock->expects($this->once())
            ->method('initMessages')
            ->willReturnSelf();

        $this->redirectMock->expects($this->once())
            ->method('getRefererUrl')
            ->willReturn('');

        $this->model->execute();
    }
}
