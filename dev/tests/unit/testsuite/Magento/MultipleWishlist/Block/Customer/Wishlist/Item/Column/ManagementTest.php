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

    public function setUp()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $wishlistHelperMock = $this->getMock(
            'Magento\MultipleWishlist\Helper\Data',
            array('getCustomer', 'isWishlistLimitReached'),
            array(),
            '',
            false
        );
        $customerMock = $this->getMock(
            '\Magento\Customer\Service\V1\Data\Customer',
            array('getId', '__wakeup'),
            array(),
            '',
            false
        );
        $customerMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(true));
        $wishlistHelperMock->expects($this->once())
            ->method('getCustomer')
            ->will($this->returnValue($customerMock));
        $wishlistMock = $this->getMock('Magento\Wishlist\Model\Wishlist', array(), array(), '', false);
        $this->wishlistListMock = $objectManagerHelper->getCollectionMock(
            'Magento\Wishlist\Model\Resource\Wishlist\Collection',
            array($wishlistMock)
        );
        $wishlistHelperMock->expects($this->once())
            ->method('isWishlistLimitReached')
            ->with($this->wishlistListMock)
            ->will($this->returnValue(false));
        $contextMock = $this->getMock(
            'Magento\Catalog\Block\Product\Context',
            array('getWishlistHelper'),
            array(),
            '',
            false
        );
        $contextMock->expects($this->once())
            ->method('getWishlistHelper')
            ->will($this->returnValue($wishlistHelperMock));
        $this->model = $objectManagerHelper->getObject(
            'Magento\MultipleWishlist\Block\Customer\Wishlist\Item\Column\Management',
            array('context' => $contextMock)
        );
    }

    public function testCanCreateWishlists()
    {
        $this->assertTrue($this->model->canCreateWishlists($this->wishlistListMock));
    }
}
