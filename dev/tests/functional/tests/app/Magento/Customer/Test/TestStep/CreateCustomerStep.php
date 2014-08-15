<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\TestStep;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Mtf\TestStep\TestStepInterface;

/**
 * Class CreateCustomerStep
 * Create customer using handler
 */
class CreateCustomerStep implements TestStepInterface
{
    /**
     * Customer fixture
     *
     * @var CustomerInjectable
     */
    protected $customer;

    /**
     * Flag for customer creation by handler
     *
     * @var bool
     */
    protected $persistCustomer;

    /**
     * @constructor
     * @param CustomerInjectable $customer
     * @param string $checkoutMethod
     */
    public function __construct(CustomerInjectable $customer, $checkoutMethod = '')
    {
        $this->customer = $customer;
        $this->persistCustomer = $checkoutMethod === 'login' ? true : false;
    }

    /**
     * Create customer
     *
     * @return array
     */
    public function run()
    {
        if ($this->persistCustomer) {
            $this->customer->persist();
        }

        return ['customer' => $this->customer];
    }
}
