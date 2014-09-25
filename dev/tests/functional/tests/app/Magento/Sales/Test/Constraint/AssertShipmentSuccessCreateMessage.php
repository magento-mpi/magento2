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
 * Class AssertShipmentSuccessCreateMessage
 * Assert success message presents
 */
class AssertShipmentSuccessCreateMessage extends AbstractConstraint
{
    /**
     * Text value to be checked
     */
    const SUCCESS_CREATE_MESSAGE = 'The shipment has been created.';

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
        \PHPUnit_Framework_Assert::assertEquals(self::SUCCESS_CREATE_MESSAGE, $successMessage);
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Success shipment create message is present.';
    }
}
