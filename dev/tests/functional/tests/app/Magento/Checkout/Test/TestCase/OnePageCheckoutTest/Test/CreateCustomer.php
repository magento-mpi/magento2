<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\TestCase\OnePageCheckoutTest\Test;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Mtf\TestCase\Step\StepInterface;

/**
 * Class CreateCustomer
 * Create customer using handler
 */
class CreateCustomer implements StepInterface
{
    /**
     * Customer fixture
     *
     * @var CustomerInjectable
     */
    protected $customer;

    /**
     * Preparing step properties
     *
     * @constructor
     * @param CustomerInjectable $customer
     */
    public function __construct(CustomerInjectable $customer)
    {
        $this->customer = $customer;
    }

    /**
     * Run step that creates customer
     *
     * @return array
     */
    public function run()
    {
        $this->customer->persist();
        return ['customer' => $this->customer];
    }
}
