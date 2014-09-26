<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Page\Adminhtml\OrderView;

/**
 * Class AssertShipmentSuccessCreateMessage
 * Assert that success message is displayed after shipment has been created
 */
class AssertShipmentSuccessCreateMessage extends AbstractConstraint
{
    /**
     * Shipment created success message
     */
    const SUCCESS_MESSAGE = 'The shipment has been created.';

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
        return 'Shipment success create message is present.';
    }
}
