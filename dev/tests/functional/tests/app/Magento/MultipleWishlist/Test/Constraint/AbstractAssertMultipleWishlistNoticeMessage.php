<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Wishlist\Test\Page\WishlistIndex;
use Magento\MultipleWishlist\Test\Fixture\MultipleWishlist;

/**
 * Abstract Class AbstractAssertMultipleWishlistNoticeMessage
 * Assert notice message is displayed on "Edit Wish List" frontend page
 */
abstract class AbstractAssertMultipleWishlistNoticeMessage extends AbstractConstraint
{
    /**
     * Public notice message
     *
     * @var string
     */
    protected $noticeMessage;

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert notice message is displayed on "Edit Wish List" frontend page
     *
     * @param CmsIndex $cmsIndex
     * @param WishlistIndex $wishlistIndex
     * @param MultipleWishlist $multipleWishlist
     * @param CustomerAccountIndex $customerAccountIndex
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        WishlistIndex $wishlistIndex,
        MultipleWishlist $multipleWishlist,
        CustomerAccountIndex $customerAccountIndex
    ) {
        $cmsIndex->open()->getLinksBlock()->openLink('My Account');
        $customerAccountIndex->getAccountMenuBlock()->openMenuItem('My Wish List');
        $wishlistIndex->getManagementBlock()->selectedWishlistByName($multipleWishlist->getName());
        \PHPUnit_Framework_Assert::assertEquals(
            $this->noticeMessage,
            $wishlistIndex->getManagementBlock()->getNoticeMessage(),
            'Wrong success message is displayed.'
        );
    }
}
