<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\CustomerSegment\Test\Fixture\CustomerSegment;
use Magento\CustomerSegment\Test\Page\Adminhtml\CustomerSegmentNew;
use Magento\CustomerSegment\Test\Page\Adminhtml\CustomerSegmentIndex;

/**
 * Class AssertCustomerSegmentMatchedCustomersInGrid
 */
class AssertCustomerSegmentMatchedCustomersInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that the customer according to search criteria presents in the grid and have correct values for
     * the following columns
     *
     * @param CustomerSegment $customerSegmentOriginal
     * @param CustomerInjectable $customer
     * @param CustomerSegmentIndex $customerSegmentIndex
     * @param CustomerSegmentNew $customerSegmentNew
     * @return void
     */
    public function processAssert(
        CustomerSegment $customerSegmentOriginal,
        CustomerInjectable $customer,
        CustomerSegmentIndex $customerSegmentIndex,
        CustomerSegmentNew $customerSegmentNew
    ) {
        $customerSegmentIndex->open();
        $website = $customerSegmentOriginal->getWebsiteIds();
        $filter = [
            'grid_segment_name' => $customerSegmentOriginal->getName(),
            'grid_segment_is_active' => $customerSegmentOriginal->getIsActive(),
            'grid_segment_website' => reset($website),
        ];
        $customerSegmentIndex->getGrid()->searchAndOpen($filter);
        $customerSegmentNew->getFormTabs()->openTab('matched_customers');

        $customerFilter = [
            'name' => $customer->getFirstname() . ' ' . $customer->getLastname(),
            'email' => $customer->getEmail(),
            'group_id' => $customer->getGroupId(),
            'telephone' => $customer->getAddress()[0]['telephone'],
            'postcode' => $customer->getAddress()[0]['postcode'],
            'country_id' => $customer->getAddress()[0]['country_id'],
            'region_id' => $customer->getAddress()[0]['region_id'],
        ];

        \PHPUnit_Framework_Assert::assertTrue(
            $customerSegmentNew->getCustomerSegmentGrid()->isRowVisible($customerFilter),
            'Customer with '
            . 'name \'' . $customerFilter['name'] . '\', '
            . 'email \'' . $customerFilter['email'] . '\' '
            . 'group_id \'' . $customerFilter['group_id'] . '\' '
            . 'telephone \'' . $customerFilter['telephone'] . '\' '
            . 'postcode \'' . $customerFilter['postcode'] . '\' '
            . 'country_id \'' . $customerFilter['country_id'] . '\' '
            . 'region_id \'' . $customerFilter['region_id'] . '\' '
            . 'is absent in Customer grid.'
        );
    }

    /**
     * Text success exist Customer in grid
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer is present in Customer grid.';
    }
}
