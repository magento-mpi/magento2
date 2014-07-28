<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Banner\Test\Page\Adminhtml\BannerIndex;
use Magento\CustomerSegment\Test\Fixture\CustomerSegment;
use Magento\CustomerSegment\Test\Page\Adminhtml\BannerNew;

/**
 * Class AssertCustomerSegmentAvailableInBannerForm
 * Assert that created customer segment is available in Banner edit page
 */
class AssertCustomerSegmentAvailableInBannerForm extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that created customer segment is available in Banner edit page
     *
     * @param BannerIndex $bannerIndex
     * @param BannerNew $bannerNew
     * @param CustomerSegment $customerSegment
     * @return void
     */
    public function processAssert(
        BannerIndex $bannerIndex,
        BannerNew $bannerNew,
        CustomerSegment $customerSegment
    ) {
        $bannerIndex->open();
        $bannerIndex->getPageActionsBlock()->addNew();
        \PHPUnit_Framework_Assert::assertTrue(
            $bannerNew->getBannerForm()->isCustomerSegmentVisible($customerSegment->getName()),
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
        return 'Customer segment is available in Banner edit page.';
    }
}
