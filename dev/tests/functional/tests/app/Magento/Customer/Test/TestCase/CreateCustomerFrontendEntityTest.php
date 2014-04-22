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
use Magento\Customer\Test\Page\HomePage;
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
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * @var HomePage
     */
    protected $homePage;

    /**
     * @param CustomerAccountCreate $customerAccountCreate
     * @param CustomerAccountLogout $customerAccountLogout
     * @param HomePage $homePage
     */
    public function __inject(
        CustomerAccountCreate $customerAccountCreate,
        CustomerAccountLogout $customerAccountLogout,
        HomePage $homePage
    ) {
        $this->customerAccountLogout = $customerAccountLogout;
        $this->customerAccountCreate = $customerAccountCreate;
        $this->homePage = $homePage;
    }

    /**
     * Create Customer account on frontend
     *
     * @param CustomerInjectable $customer
     */
    public function testCreateCustomer(CustomerInjectable $customer)
    {
       //Steps
       $this->homePage->open();
       $this->homePage->getHomePage()->clickRegisterButton();
       $this->customerAccountCreate->getCreateForm()->create($customer);
    }

    /**
     * Logout customer from frontend account
     *
     * return void
     */
    public function tearDown()
    {
        $this->customerAccountLogout->open();
    }
}
