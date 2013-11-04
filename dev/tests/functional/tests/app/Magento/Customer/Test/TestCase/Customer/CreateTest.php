<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\TestCase\Customer;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Customer\Test\Fixture\Customer;

/**
 * Create Customer on frontend and set default billing address
 *
 * @package Magento\Customer\Test\TestCase\Customer;
 */
class CreateTest extends Functional
{
    /**
     * Create Customer account on frontend
     *
     * @ZephyrId MAGETWO-12394
     * @param Customer $fixture injectable
     */
    public function testCreateCustomer(Customer $fixture)
    {
        $createPage = Factory::getPageFactory()->getCustomerAccountCreate();

        //Step 1 Create Account
        $createPage->open();
        $createPage->getCreateForm()->create($fixture);

        //Verifying
        $accountIndexPage = Factory::getPageFactory()->getCustomerAccountIndex();
        $messages = $accountIndexPage->getMessages();
        $this->assertContains('Thank you for registering', $messages->getSuccessMessages());

        //Check that customer redirected to Dashboard after registration
        $this->assertTrue($accountIndexPage->getDashboardHeaderPanelTitle()->isVisible());

        //Step 2 Set Billing Address
        $accountIndexPage->getAddressBook()->editBillingAddress();
        $addressEditPage = Factory::getPageFactory()->getCustomerAddressEdit();
        $addressEditPage->getEditForm()->saveAddress($fixture->getDefaultAddress());

        //Verifying
        $accountIndexPage = Factory::getPageFactory()->getCustomerAccountIndex();
        $messages = $accountIndexPage->getMessages();
        $this->assertContains('The address has been saved', $messages->getSuccessMessages());
    }
}
