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
        //Pages & Blocks
        $customerPage = Factory::getPageFactory()->getCustomer();
        $gridBlock = $customerPage->getCustomerGridBlock();
        $pageActionsBlock = $customerPage->getPageActionsBlock();
        $customerCreatePage = Factory::getPageFactory()->getCustomerNew();
        $newCustomerForm = $customerCreatePage->getNewCustomerForm();
        $messagesBlock = $customerCreatePage->getMessageBlock();
        //Steps
        $customerPage->open();
        $pageActionsBlock->clickAddNew();
        $newCustomerForm->fill($customerFixture);
        $newCustomerForm->clickSaveAndContinue();
        $messagesBlock->assertSuccessMessage($customerFixture);
        //Verifying
        $customerPage->open();
        $this->assertTrue($gridBlock->isRowVisible(array(
            'email' => $customerFixture->getEmail()
        )), 'Customer email "' . $customerFixture->getEmail() . '" not found in the grid');
    }
}
