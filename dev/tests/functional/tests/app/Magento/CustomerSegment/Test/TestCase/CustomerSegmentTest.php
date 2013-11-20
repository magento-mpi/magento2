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

namespace Magento\CustomerSegment\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;

class CustomerSegmentTest extends Functional
{
    /**
     * Login to backend as a precondition to test
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
    public function testCreateCustomerSegment()
    {
        // Start Add Customer Precondition
        //Data
        $customerFixture = Factory::getFixtureFactory()->getMagentoCustomerCustomer();
        $customerFixture->switchData('backend_retailer_customer');
        Factory::getApp()->magentoCustomerCreateCustomerBackend($customerFixture);

        //Pages & Blocks
        $customerPage = Factory::getPageFactory()->getCustomer();
        $gridBlock = $customerPage->getCustomerGridBlock();
        $pageActionsBlock = $customerPage->getPageActionsBlock();
        $customerCreatePage = Factory::getPageFactory()->getCustomerNew();
        $newCustomerForm = $customerCreatePage->getNewCustomerForm();
        $messagesBlock = $customerCreatePage->getMessageBlock();

        //Verifying
        $customerPage->open();
        $this->assertTrue($gridBlock->isRowVisible(array(
            'email' => $customerFixture->getEmail()
        )), 'Customer email "' . $customerFixture->getEmail() . '" not found in the grid');
        // End Add Customer

        //CustomerSegment test here
        //data
        $customerSegmentFixture = Factory::getFixtureFactory()->getMagentoCustomerSegmentCustomerSegment();
        //pages&blocks
        $customerSegmentPage = Factory::getPageFactory()->getAdminCustomersegment();
        $pageActionsBlockCs = $customerSegmentPage->getPageActionsBlock();
        $customerSegmentCreatePage = Factory::getPageFactory()->getAdminCustomersegmentNew();
        $newCustomerSegmentForm = $customerSegmentCreatePage->getNewCustomerSegmentForm();
        $messagesBlock = $customerSegmentCreatePage->getMessageBlock();
        //steps
        $customerSegmentPage->open();
        $pageActionsBlockCs->clickAddNew();
        $newCustomerSegmentForm->fill($customerSegmentFixture);
        $newCustomerSegmentForm->clickSaveAndContinue();
        $messagesBlock->assertSuccessMessage($customerFixture);
        // add a condition
        $tabsWidget = $customerSegmentCreatePage->getConditionsTab();
        $tabsWidget->openTab('magento_customersegment_segment_tabs_conditions_section');
        $addConditionWidget = $customerSegmentCreatePage->getConditionsAdd();
        $addConditionWidget->clickAddNew();
        $customerSegmentConditionsFixture = Factory::getFixtureFactory()->getMagentoCustomerSegmentCustomerSegment();
    }
}
