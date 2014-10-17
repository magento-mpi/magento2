<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\MultipleWishlist\Block\Customer\Wishlist\Item\Column\Management
 */
namespace Magento\MultipleWishlist\Block\Customer\Wishlist\Item\Column;

class ManagementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Management
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Wishlist\Model\Resource\Wishlist\Collection
     */
    protected $wishlistListMock;

    /**
     * @var \Magento\MultipleWishlist\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $wishlistHelperMock;

    /**
     * @var \Magento\Customer\Service\V1\Data\Customer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataCustomerMock;

    /**
     * @var \Magento\Wishlist\Model\Wishlist|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $wishlistMock;

    /**
     * @var \Magento\Catalog\Block\Product\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    public function setUp()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->dataCustomerMock = $this->getMockBuilder('Magento\Customer\Service\V1\Data\Customer')
            ->disableOriginalConstructor()
            ->setMethods(array('getId', '__wakeup'))
            ->getMock();

        $this->wishlistHelperMock = $this->getMockBuilder('Magento\MultipleWishlist\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();

        $this->wishlistMock = $this->getMockBuilder('Magento\Wishlist\Model\Wishlist')
            ->disableOriginalConstructor()
            ->getMock();

        $this->wishlistListMock = $objectManagerHelper->getCollectionMock(
            'Magento\Wishlist\Model\Resource\Wishlist\Collection',
            array($this->wishlistMock)
        );

        $this->contextMock = $this->getMockBuilder('Magento\Catalog\Block\Product\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $this->contextMock->expects($this->once())
            ->method('getWishlistHelper')
            ->will($this->returnValue($this->wishlistHelperMock));

        $this->model = $objectManagerHelper->getObject(
            'Magento\MultipleWishlist\Block\Customer\Wishlist\Item\Column\Management',
            array('context' => $this->contextMock)
        );
    }

    public function testCanCreateWishlistsNoCustomer()
    {
        $this->wishlistHelperMock->expects($this->once())
            ->method('getCustomer')
            ->will($this->returnValue(false));

        $this->assertFalse($this->model->canCreateWishlists($this->wishlistListMock));
    }

    public function testCanCreateWishlists()
    {
        $this->dataCustomerMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(true));

        $this->wishlistHelperMock->expects($this->once())
            ->method('getCustomer')
            ->will($this->returnValue($this->dataCustomerMock));
        $this->wishlistHelperMock->expects($this->once())
            ->method('isWishlistLimitReached')
            ->with($this->wishlistListMock)
            ->will($this->returnValue(false));

        $this->assertTrue($this->model->canCreateWishlists($this->wishlistListMock));
    }

    public function testCanCreateWishlistsLimitReached()
    {
        $this->wishlistHelperMock->expects($this->once())
            ->method('getCustomer')
            ->will($this->returnValue($this->dataCustomerMock));
        $this->wishlistHelperMock->expects($this->once())
            ->method('isWishlistLimitReached')
            ->with($this->wishlistListMock)
            ->will($this->returnValue(true));

        $this->assertFalse($this->model->canCreateWishlists($this->wishlistListMock));
    }

    public function testCanCreateWishlistsNoCustomerId()
    {
        $this->dataCustomerMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(false));

        $this->wishlistHelperMock->expects($this->once())
            ->method('getCustomer')
            ->will($this->returnValue($this->dataCustomerMock));
        $this->wishlistHelperMock->expects($this->once())
            ->method('isWishlistLimitReached')
            ->with($this->wishlistListMock)
            ->will($this->returnValue(false));

        $this->assertFalse($this->model->canCreateWishlists($this->wishlistListMock));
    }
}
