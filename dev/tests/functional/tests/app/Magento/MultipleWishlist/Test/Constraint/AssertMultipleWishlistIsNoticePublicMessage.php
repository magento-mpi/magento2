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
     * Public notice message
     *
     * @var string
     */
    protected $noticeMessage = 'This wishlist is publicly visible.';

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Public notice message is accepted.';
    }
}
