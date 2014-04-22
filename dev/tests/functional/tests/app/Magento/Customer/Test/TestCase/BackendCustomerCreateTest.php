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

class BackendCustomerCreateTest extends Functional
{
    /**
     * Login into backend area before test
     */
    protected function setUp()
    {
        Factory::getApp()->magentoBackendLoginUser();
    }

    /**
     * New customer creation in backend
     *
     * @ZephyrId MAGETWO-12516
     */
    public function testCreateCustomer()
    {
        //Data
        $customerFixture = Factory::getFixtureFactory()->getMagentoCustomerCustomer();
        $customerFixture->switchData('backend_customer');
        $searchData = array(
            'email' => $customerFixture->getEmail()
        );
        //Pages
        $customerPage = Factory::getPageFactory()->getCustomerIndex();
        $customerCreatePage = Factory::getPageFactory()->getCustomerIndexNew();

        //Steps
        $customerPage->open();
        $customerPage->getPageActionsBlock()->addNew();
        $customerCreatePage->getCustomerForm()->fill($customerFixture);
        $customerCreatePage->getPageActionsBlock()->saveAndContinue();
        $customerCreatePage->getMessagesBlock()->assertSuccessMessage();

        //Verifying
        $customerPage->open();
        $this->assertTrue(
            $customerPage->getCustomerGridBlock()->isRowVisible($searchData),
            'Customer email "' . $searchData['email'] . '" not found in the grid'
        );
    }
}
