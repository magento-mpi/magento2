<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CustomerSegment\Test\Constraint;

use Magento\CustomerSegment\Test\Fixture\CustomerSegment;
use Magento\CustomerSegment\Test\Page\Adminhtml\CustomerSegmentIndex;
use Magento\CustomerSegment\Test\Page\Adminhtml\CustomerSegmentNew;
use Mtf\Constraint\AbstractAssertForm;

/**
 * Class AssertCustomerSegmentForm
 * Assert that displayed segment data on edit page is equals passed from fixture
 */
class AssertCustomerSegmentForm extends AbstractAssertForm
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Skipped fields for verify data
     *
     * @var array
     */
    protected $skippedFields = ['conditions_serialized', 'segment_id'];

    /**
     * Assert that displayed segment data on edit page is equals passed from fixture
     *
     * @param CustomerSegment $customerSegment
     * @param CustomerSegmentIndex $customerSegmentIndex
     * @param CustomerSegmentNew $customerSegmentNew
     * @return void
     */
    public function processAssert(
        CustomerSegment $customerSegment,
        CustomerSegmentIndex $customerSegmentIndex,
        CustomerSegmentNew $customerSegmentNew
    ) {
        $customerSegmentIndex->open();
        $filter = [
            'grid_segment_name' => $customerSegment->getName(),
        ];
        $customerSegmentIndex->getGrid()->searchAndOpen($filter);

        $formData = $customerSegmentNew->getCustomerSegmentForm()->getData();
        $dataDiff = $this->verifyData($customerSegment->getData(), $formData, false, false);
        \PHPUnit_Framework_Assert::assertEmpty(
            $dataDiff,
            'Customer Segments data not equals to passed from fixture.'
            . "\nLog:\n" . implode(";\n", $dataDiff)
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer Segments page data on edit page equals data from fixture.';
    }
}
