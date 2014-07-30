<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Customer\Test\Fixture\AddressInjectable;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\CustomerSegment\Test\Page\Adminhtml\CustomerSegmentReportDetail;

/**
 * Class AssertCustomerSegmentReportInGrid
 * Assert that created customer is present in customer segment report grid
 */
class AssertCustomerSegmentReportInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that created customer segment report presents in the grid and customer from it has correct values
     * for the following columns:
     * - name
     * - email
     * - group
     * - phone
     * - ZIP
     * - country
     * - state/province
     *
     * @param array $foundCustomers
     * @param array $customers
     * @param CustomerSegmentReportDetail $reportDetailPage
     * @return void
     */
    public function processAssert(
        array $foundCustomers,
        array $customers,
        CustomerSegmentReportDetail $reportDetailPage
    ) {
        $errors = [];
        foreach ($foundCustomers as $index) {
            /** @var CustomerInjectable $customer */
            $customer = $customers[$index];
            /** @var AddressInjectable $address */
            $address = $customer->getDataFieldConfig('address')['source']->getAddresses()[0];
            $filter = [
                'grid_name' => $address->getFirstname() . ' ' . $address->getLastname(),
                'grid_email' => $customer->getEmail(),
                'grid_group' => $customer->getGroupId(),
                'grid_telephone' => $address->getTelephone(),
                'grid_billing_postcode' => $address->getPostcode(),
                'grid_billing_country_id' => $address->getCountryId(),
                'grid_billing_region' => $address->getRegionId()
            ];

            if (!$reportDetailPage->getDetailGrid()->isRowVisible($filter)) {
                $errors[] = '- row "' . implode(', ', $filter) . '" was not found in the grid report';
            }
        }

        \PHPUnit_Framework_Assert::assertEmpty(
            $errors,
            'When checking the grid, the following errors were found:' . PHP_EOL . implode(PHP_EOL, $errors)
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'All required customers are present in customer segment report grid.';
    }
}
