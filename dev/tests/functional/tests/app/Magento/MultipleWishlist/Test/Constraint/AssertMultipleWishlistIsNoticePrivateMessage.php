<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\Constraint;

/**
 * Class AssertMultipleWishlistIsPrivateNoticeMessage
 * Assert private notice message is displayed on "Edit Wish List" frontend page
 */
class AssertMultipleWishlistIsNoticePrivateMessage extends AbstractAssertMultipleWishlistNoticeMessage
{
    /**
     * Successful message
     *
     * @var string
     */
    protected $successfulMessage = 'Private notice message is accept.';

    /**
     * Private notice message
     *
     * @var string
     */
    protected $noticeMessage = 'This wishlist is private. Only you can view this wishlist.';
}
