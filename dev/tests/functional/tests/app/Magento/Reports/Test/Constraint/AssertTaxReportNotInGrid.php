<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\Constraint;

use Magento\Reports\Test\Page\Adminhtml\SalesTaxReport;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Tax\Test\Fixture\TaxRule;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertTaxReportNotInGrid
 * Check that Tax report is absent on tax report page
 */
class AssertTaxReportNotInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert Tax report is absent on tax report page
     *
     * @param SalesTaxReport $salesTaxReport
     * @param OrderInjectable $order
     * @param TaxRule $taxRule
     * @param string $taxAmount
     * @return void
     */
    public function processAssert(
        SalesTaxReport $salesTaxReport,
        OrderInjectable $order,
        TaxRule $taxRule,
        $taxAmount
    ) {
        $filter = [
            'tax' => $taxRule->getTaxRate()[0],
            'rate' => $taxRule->getDataFieldConfig('tax_rate')['source']->getFixture()[0]->getRate(),
            'orders' => count($order->getEntityId()['products']),
            'tax_amount' => $taxAmount
        ];

        \PHPUnit_Framework_Assert::assertFalse(
            $salesTaxReport->getGridBlock()->isRowVisible($filter, false),
            "Tax Report is visible."
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return "Tax Report is absent on tax report page.";
    }
}
