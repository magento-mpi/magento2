<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\Constraint;

use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Constraint\AbstractConstraint;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\MultipleWishlist\Test\Fixture\MultipleWishlist;
use Magento\MultipleWishlist\Test\Page\MultipleWishlistIndex;

/**
 * Class AssertWishlistIsPublicNoticeMessage
 * Assert public notice message is displayed on "Edit Wish List" frontend page.
 */
class AssertWishlistIsPublicNoticeMessage extends AbstractConstraint
{
    /**
     * Public notice message
     *
     * @var string
     */
    protected $noticeMessage = 'This wishlist is publicly visible.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert public notice message is displayed on "Edit Wish List" frontend page.
     *
     * @param CmsIndex $cmsIndex
     * @param MultipleWishlistIndex $multipleWishlistIndex
     * @param MultipleWishlist $multipleWishlist
     * @param CustomerAccountIndex $customerAccountIndex
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        MultipleWishlistIndex $multipleWishlistIndex,
        MultipleWishlist $multipleWishlist,
        CustomerAccountIndex $customerAccountIndex
    ) {
        $cmsIndex->open()->getLinksBlock()->openLink('My Account');
        $customerAccountIndex->getAccountMenuBlock()->openMenuItem('My Wish List');
        $multipleWishlistIndex->getManagementBlock()->selectedWishlistByName($multipleWishlist->getName());
        \PHPUnit_Framework_Assert::assertEquals(
            $this->noticeMessage,
            $multipleWishlistIndex->getManagementBlock()->getNoticeMessage(),
            'Wrong success message is displayed.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Notice message is accept.';
    }
}
