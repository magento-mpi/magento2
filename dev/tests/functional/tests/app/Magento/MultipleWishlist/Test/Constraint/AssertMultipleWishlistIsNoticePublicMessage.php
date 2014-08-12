<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\Constraint;

/**
 * Class AssertMultipleWishlistIsPublicNoticeMessage
 * Assert public notice message is displayed on "Edit Wish List" frontend page
 */
class AssertMultipleWishlistIsNoticePublicMessage extends AbstractAssertMultipleWishlistNoticeMessage
{
    /**
     * Successful message
     *
     * @var string
     */
    protected $successfulMessage = 'Public notice message is accept.';

    /**
     * Public notice message
     *
     * @var string
     */
    protected $noticeMessage = 'This wishlist is publicly visible.';
}
