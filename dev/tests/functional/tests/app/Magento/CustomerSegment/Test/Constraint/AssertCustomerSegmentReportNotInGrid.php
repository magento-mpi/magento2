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
 * Class AssertCustomerSegmentReportNotInGrid
 * Assert that created customer is absent in a customer segment grid
 */
class AssertCustomerSegmentReportNotInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that created customer is absent in a customer segment grid
     *
     * @param array $notFoundCustomers
     * @param array $customers
     * @param CustomerSegmentReportDetail $reportDetailPage
     * @return void
     */
    public function processAssert(
        array $notFoundCustomers,
        array $customers,
        CustomerSegmentReportDetail $reportDetailPage
    ) {
        $errors = [];
        foreach ($notFoundCustomers as $index) {
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

            if ($reportDetailPage->getDetailGrid()->isRowVisible($filter)) {
                $errors[] = '- row "' . implode(', ', $filter) . '" was found in the grid report';
            }
        }

        \PHPUnit_Framework_Assert::assertEmpty(
            $errors,
            'When checking the report grid, the following errors were found:' . PHP_EOL . implode(PHP_EOL, $errors)
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Report grid does not contain customers which must be absent.';
    }
}
