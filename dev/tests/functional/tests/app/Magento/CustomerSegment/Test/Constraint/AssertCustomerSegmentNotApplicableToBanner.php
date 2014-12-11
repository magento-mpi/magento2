<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CustomerSegment\Test\Constraint;

use Magento\Banner\Test\Page\Adminhtml\BannerNew;
use Magento\CustomerSegment\Test\Fixture\CustomerSegment;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCustomerSegmentNotApplicableToBanner
 * Assert that removed customer segment cannot be selected while creating a banner
 */
class AssertCustomerSegmentNotApplicableToBanner extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

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
