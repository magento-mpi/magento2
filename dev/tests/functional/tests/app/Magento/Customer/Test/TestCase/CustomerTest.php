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
 * Class CustomerTest.
 * Test for customer frontend page.
 *
 * @package Magento\Customer\Test\TestCase;
 */
class CustomerTest extends Functional
{
    /**
     * Create new customer by handler and login with its credentials.
     *
     * @param Customer $fixture injectable
     */
    public function testLoginCustomer(Customer $fixture)
    {
        //Data
        $fixture->persist();
        //Page
        $loginPage = Factory::getPageFactory()->getCustomerAccountLogin();
        //Steps
        $loginPage->open();
        $loginPage->getLoginBlock()->login($fixture);
        //Verifying
        $this->assertTrue($loginPage->getDashboardHeaderPanelTitle()->isVisible());
    }
}
