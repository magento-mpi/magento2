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

namespace Magento\Customer\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Customer\Test\Fixture\Customer;

/**
 * Create Customer on frontend and set default billing address
 *
 * @package Magento\Customer\Test\TestCase;
 */
class CreateOnFrontendTest extends Functional
{
    /**
     * Create Customer account on frontend
     *
     * @ZephyrId MAGETWO-12394
     */
    public function testCreateCustomer()
    {
        //Data
        $customer = Factory::getFixtureFactory()->getMagentoCustomerCustomer();
        $customer->switchData('customer_US_1');

        //Page
        $homePage = Factory::getPageFactory()->getCmsIndexIndex();
        $createPage = Factory::getPageFactory()->getCustomerAccountCreate();
        $accountIndexPage = Factory::getPageFactory()->getCustomerAccountIndex();
        $addressEditPage = Factory::getPageFactory()->getCustomerAddressEdit();

        //Step 1 Create Account
        $homePage->open();
        $topLinks = $homePage->getTopLinks();
        $topLinks->openLink('register');

        $createPage->getCreateForm()->create($customer);

        //Verifying
        $messages = $accountIndexPage->getMessages();
        $this->assertContains('Thank you for registering', $messages->getSuccessMessages());

        //Check that customer redirected to Dashboard after registration
        $this->assertTrue($accountIndexPage->getDashboardHeaderPanelTitle()->isVisible());

        //Step 2 Set Billing Address
        $accountIndexPage->getDashboardAddress()->editBillingAddress();
        $addressEditPage->getEditForm()->editCustomerAddress($customer->getAddressData());

        //Verifying
        $accountIndexPage = Factory::getPageFactory()->getCustomerAccountIndex();
        $messages = $accountIndexPage->getMessages();
        $this->assertContains('The address has been saved', $messages->getSuccessMessages());

        //Verify customer address against previously entered data
        $accountIndexPage->open();
        $accountIndexPage->getDashboardAddress()->editBillingAddress();
        $addressEditPage = Factory::getPageFactory()->getCustomerAddressEdit();
        $this->assertTrue($addressEditPage->getEditForm()->verify($customer->getAddressData()));
    }
}
