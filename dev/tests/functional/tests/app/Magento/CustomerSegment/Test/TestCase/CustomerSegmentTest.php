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
     * New customer segment creation in backend
     *
     * @ZephyrId MAGETWO-12393
     */
    public function testCreateCustomerSegment()
    {
        // Start Add Customer Precondition
        //Data
        $customerFixture = Factory::getFixtureFactory()->getMagentoCustomerCustomer();
        $customerFixture->switchData('backend_retailer_customer');
        Factory::getApp()->magentoCustomerCreateCustomerBackend($customerFixture);
        // Pages & Blocks
        $customerPage = Factory::getPageFactory()->getCustomer();
        $gridBlock = $customerPage->getCustomerGridBlock();
        // Verifying
        $customerPage->open();
        $this->assertTrue($gridBlock->isRowVisible(array(
            'email' => $customerFixture->getEmail()
        )), 'Customer email "' . $customerFixture->getEmail() . '" not found in the grid');
        // Start Customer Segment test
        // data
        $customerSegmentFixture = Factory::getFixtureFactory()->getMagentoCustomerSegmentCustomerSegment();
        $conditionsFixture = Factory::getFixtureFactory()->getMagentoCustomerSegmentSegmentCondition();
        $conditionType = $conditionsFixture->getConditionType();
        $conditionValue = $conditionsFixture->getConditionValue();
        // pages & blocks
        $customerSegmentPage = Factory::getPageFactory()->getAdminCustomersegment();
        $pageActionsBlockCs = $customerSegmentPage->getPageActionsBlock();
        $customerSegmentCreatePage = Factory::getPageFactory()->getAdminCustomersegmentNew();
        $newCustomerSegmentForm = $customerSegmentCreatePage->getNewCustomerSegmentForm();
        $messagesBlock = $customerSegmentCreatePage->getMessageBlock();
        // begin steps to add a customer segment
        $customerSegmentPage->open();
        // add General Properties
        $pageActionsBlockCs->clickAddNew();
        $newCustomerSegmentForm->fill($customerSegmentFixture);
        $newCustomerSegmentForm->clickSaveAndContinue();
        $messagesBlock->assertSuccessMessage();
        // open conditions tab
        $tabsWidget = $customerSegmentCreatePage->getConditionsTab();
        $tabsWidget->openTab('magento_customersegment_segment_tabs_conditions_section');
        // add a condition
        $addWidget = $customerSegmentCreatePage->getConditions();
        $addWidget->clickAddNew();
        $selectWidget = $customerSegmentCreatePage->getConditions();
        $selectWidget->selectCondition($conditionType);
        $optionWidget = $customerSegmentCreatePage->getConditions();
        $optionWidget->clickEllipsis();
        $valueWidget = $customerSegmentCreatePage->getConditions();
        $valueWidget->selectConditionValue($conditionValue);
        $saveWidget = $customerSegmentCreatePage->getSave();
        $saveWidget->clickSaveAndContinue();
        $customerSegmentCreatePage->setMessageBlock();
        $conditionMessageBlock = $customerSegmentCreatePage->getMessageBlock();
        $conditionMessageBlock->assertSuccessMessage();
        // open matched customers tab
        $customerTabWidget = $customerSegmentCreatePage->getCustomersTab();
        $customerTabWidget->openTab('magento_customersegment_segment_tabs_customers_tab');
        $customerSegmentCreatePage->setMessageBlock();
        $customerMessageBlock = $customerSegmentCreatePage->getMessageBlock();
        $customerMessageBlock->assertSuccessMessage();
        // verify matched customers
        $customerGridBlock = $customerSegmentCreatePage->getCustomerGridBlock();
        $customerGridBlock->search(array('email' => $customerFixture->getEmail()));
        $this->assertEquals($customerFixture->getEmail(),
            $customerGridBlock->getGridEmail(),
            'Matched Customer Email "' . $customerFixture->getEmail() . '" not found in the grid'
        );
        $this->assertEquals($customerFixture->getGroup(),
            $customerGridBlock->getGridGroup(),
            'Matched Customer Group "' . $customerFixture->getGroup() . '" not found in the grid'
        );
        $fixtureName = $customerFixture->getFirstName() . " " . $customerFixture->getLastName();
        $this->assertEquals($fixtureName,
            $customerGridBlock->getGridName(),
            'Matched Customer Name "' . $fixtureName . '" not found in the grid'
        );
    }
}
