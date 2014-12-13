<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CustomerSegment\Test\Constraint;

use Magento\Banner\Test\Page\Adminhtml\BannerNew;
use Magento\CustomerSegment\Test\Fixture\CustomerSegment;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCustomerSegmentAvailableInBannerForm
 * Assert that created customer segment is available in Banner edit page
 */
class AssertCustomerSegmentAvailableInBannerForm extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that created customer segment is available in Banner edit page
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
        \PHPUnit_Framework_Assert::assertTrue(
            $bannerNew->getSegmentBannerForm()->isCustomerSegmentVisible($customerSegment->getName()),
            'Customer segment is not available in Banner edit page.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer segment is available on Banner edit page.';
    }
}
