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
     * Test creation customer on backend
     */
    public function testCreateCustomer()
    {
        $customerViewPage = Factory::getPageFactory()->getAdminCustomer();
        $gridBlock = $customerViewPage->getCustomerGridBlock();
        $pageActionsBlock = $customerViewPage->getPageActionsBlock();
        $customerCreatePage = Factory::getPageFactory()->getAdminCustomerNew();
        $newCustomerForm = $customerCreatePage->getNewCustomerForm();
        $customerFixture = Factory::getFixtureFactory()->getMagentoCustomerCustomer();

        $customerViewPage->open();
        $pageActionsBlock->clickAddNew();

        $newCustomerForm->fill($customerFixture);
        $newCustomerForm->clickSaveAndContinue();
        $customerCreatePage->waitForCustomerSaveSuccess();

        //Check created customer
        $customerViewPage->open();
        $this->assertTrue($gridBlock->isRowVisible(array(
            'email' => $customerFixture->getEmail()
        )), 'Customer email "' . $customerFixture->getEmail() . '" not found in the grid');
    }
}
