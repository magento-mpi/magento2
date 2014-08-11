<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\Constraint;

/**
 * Class AssertWishlistIsPrivateNoticeMessage
 * Assert private notice message is displayed on "Edit Wish List" frontend page.
 */
class AssertWishlistIsPrivateNoticeMessage extends AssertWishlistIsPublicNoticeMessage
{
    /**
     * Private notice message
     *
     * @var string
     */
    protected $noticeMessage = 'This wishlist is private. Only you can view this wishlist.';
}
