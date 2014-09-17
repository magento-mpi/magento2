<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertInvoiceWithShipmentSuccessMessage
 * Assert success created the invoice and shipment message presents
 */
class AssertInvoiceWithShipmentSuccessMessage extends AbstractConstraint
{
    /**
     * Text value to be checked
     */
    const SUCCESS_MESSAGE = 'You created the invoice and shipment.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert success message presents
     *
     * @param string $successMessage
     * @return void
     */
    public function processAssert($successMessage)
    {
        \PHPUnit_Framework_Assert::assertEquals(self::SUCCESS_MESSAGE, $successMessage);
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Success invoice and shipment create message is present.';
    }
}
