<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Page\SalesGuestPrint;
use Magento\Customer\Test\Fixture\AddressInjectable;

/**
 * Assert that BillingAddress printed correctly on sales guest print page.
 */
class AssertSalesPrintOrderBillingAddress extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that BillingAddress printed correctly on sales guest print page.
     *
     * @param SalesGuestPrint $salesGuestPrint
     * @param AddressInjectable $billingAddress
     * @return void
     */
    public function processAssert(SalesGuestPrint $salesGuestPrint, AddressInjectable $billingAddress)
    {
        $addressRenderer = $this->objectManager->create(
            'Magento\Customer\Test\Block\Address\Renderer',
            ['address' => $billingAddress, 'type' => 'html']
        );
        $expectedBillingAddress = $addressRenderer->render();
        \PHPUnit_Framework_Assert::assertEquals(
            $expectedBillingAddress,
            $salesGuestPrint->getInfoBlock()->getBillingAddress(),
            "Billing address was printed incorrectly."
        );
    }

    /**
     * Returns a string representation of successful assertion.
     *
     * @return string
     */
    public function toString()
    {
        return "Billing address printed correctly.";
    }
}
