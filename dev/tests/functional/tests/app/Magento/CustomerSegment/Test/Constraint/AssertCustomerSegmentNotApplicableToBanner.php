<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\CustomerSegment\Test\Fixture\CustomerSegment;
use Magento\Banner\Test\Page\Adminhtml\BannerNew;

/**
 * Class AssertCustomerSegmentNotApplicableToBanner
 * Assert that removed customer segment cannot be selected while creating a banner
 */
class AssertCustomerSegmentNotApplicableToBanner extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that created customer segment is not available in Banner edit page
     *
     * @param BannerNew $bannerNew
     * @param CustomerSegment $customerSegment
     * @return void
     */
    public function processAssert(
        BannerNew $bannerNew,
        CustomerSegment $customerSegment
    ) {
        $bannerNew->open();
        \PHPUnit_Framework_Assert::assertFalse(
            $bannerNew->getSegmentBannerForm()->isCustomerSegmentVisible($customerSegment->getName()),
            'Customer segment is available on Banner edit page.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer segment is not available on Banner edit page.';
    }
}
