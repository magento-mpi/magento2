<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\TestCase;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\CustomerAccountCreate;
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for CreateCustomerFrontendEntity
 *
 * Test Flow:
 * 1. Go to frontend.
 * 2. Click Register link.
 * 3. Fill registry form.
 * 4. Click 'Create account' button.
 * 5. Perform assertions.
 *
 * @group Customer_Account_(CS)
 * @ZephyrId MAGETWO-23546
 */
class CreateCustomerFrontendEntityTest extends Injectable
{
    /**
     * @var CustomerAccountCreate
     */
    protected $customerAccountCreate;

    /**
     * @var \Magento\Customer\Test\Page\CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * @param CustomerAccountCreate $customerAccountCreate
     * @param CustomerAccountLogout $customerAccountLogout
     */
    public function __inject(
        CustomerAccountCreate $customerAccountCreate,
        CustomerAccountLogout $customerAccountLogout
    ) {
        $this->customerAccountLogout = $customerAccountLogout;
        $this->customerAccountCreate = $customerAccountCreate;
    }

    /**
     * Create Customer account on frontend
     *
     * @param CustomerInjectable $customer
     */
    public function testCreateCustomer(CustomerInjectable $customer)
    {
       //Steps
       $this->customerAccountCreate->open();
       $this->customerAccountCreate->getCreateForm()->fill($customer);
       $this->customerAccountCreate->getCreateForm()->registerCustomer();
    }

    /**
     * return void
     */
    public function tearDown()
    {
        $this->customerAccountLogout->open();
    }
}
