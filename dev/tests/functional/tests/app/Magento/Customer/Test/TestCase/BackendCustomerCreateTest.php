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
    public function testCreateCustomer()
    {
        $customerViewPage = Factory::getPageFactory()->getAdminCustomer();
        $gridBlock = $customerViewPage->getCustomerGridBlock();
        $pageActionsBlock = $customerViewPage->getPageActionsBlock();
        $customerCreatePage = Factory::getPageFactory()->getAdminCustomerNew();
        $newCustomerForm = $customerCreatePage->getNewCustomerForm();
        $customerFixture = Factory::getFixtureFactory()->getMagentoCustomerCustomer();

        Factory::getApp()->magentoBackendLoginUser();
        $customerViewPage->open();
        $pageActionsBlock->addNew();

        $newCustomerForm->fill($customerFixture);
        $newCustomerForm->saveContinue();
        $customerCreatePage->waitForCustomerSaveSuccess();

        //Check created customer
        $customerViewPage->open();
        $this->assertTrue($gridBlock->isRowVisible(array(
            'email' => $customerFixture->getEmail()
        )), 'Customer email "' . $customerFixture->getEmail() . '" not found in the grid');
    }
}
