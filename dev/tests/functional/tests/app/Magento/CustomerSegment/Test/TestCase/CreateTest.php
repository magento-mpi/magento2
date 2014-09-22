<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;

/**
 * Create Customer Segment on backend
 */
class CreateTest extends Functional
{
    /**
     * Login to backend as a precondition to test
     *
     * @return void
     */
    protected function setUp()
    {
        Factory::getApp()->magentoBackendLoginUser();
    }

    /**
     * New Customer Segment creation for specific Customer Group
     *
     * @return void
     * @ZephyrId MAGETWO-12393
     */
    public function testCreateCustomerSegment()
    {
        // Start Add Customer Precondition
        //Data
        $customerFixture = Factory::getFixtureFactory()->getMagentoCustomerCustomerBackend();
        $customerFixture->switchData('backend_retailer_customer');
        $customerFixture->persist();

        // Start Customer Segment test
        // data
        $customerSegmentFixture = Factory::getFixtureFactory()->getMagentoCustomerSegmentSegmentGeneralProperties();
        // pages & blocks
        $customerSegmentPage = Factory::getPageFactory()->getCustomersegmentIndex();
        $customerSegmentCreatePage = Factory::getPageFactory()->getCustomersegmentIndexNew();
        $newCustomerSegmentForm = $customerSegmentCreatePage->getCustomerSegmentForm();
        // begin steps to add a customer segment
        $customerSegmentPage->open();
        $customerSegmentPage->getPageActionsBlock()->addNew();
        // fill General Properties
        $newCustomerSegmentForm->fill($customerSegmentFixture);
        $customerSegmentCreatePage->getPageMainActions()->saveAndContinue();
        $customerSegmentCreatePage->getMessagesBlock()->assertSuccessMessage();
        // save conditions tab
        $objectManager = Factory::getObjectManager();
        $fixtureConditions = $objectManager->create(
            '\Magento\CustomerSegment\Test\Fixture\CustomerSegment',
            ['data' => ['conditions_serialized' => '[Group|is|Retailer]']]
        );
        $customerSegmentCreatePage->getCustomerSegmentForm()->openTab('conditions');
        // add condition
        $newCustomerSegmentForm->fill($fixtureConditions);
        $customerSegmentCreatePage->getPageMainActions()->saveAndContinue();

        $conditionMessagesBlock = $customerSegmentCreatePage->getMessagesBlock();
        $conditionMessagesBlock->assertSuccessMessage();
        // open matched customers tab
        $customerSegmentCreatePage->getCustomerSegmentForm()->openTab('matched_customers');
        // verify matched customers
        $customerGridBlock = $customerSegmentCreatePage->getCustomerSegmentForm()->getMatchedCustomers()
            ->getCustomersGrid();
        $customerGridBlock->search(['grid_email' => $customerFixture->getEmail()]);
        $this->assertEquals(
            $customerFixture->getEmail(),
            $customerGridBlock->getGridEmail(),
            'Matched Customer Email "' . $customerFixture->getEmail() . '" not found in the grid'
        );
        $this->assertEquals(
            $customerFixture->getGroup(),
            $customerGridBlock->getGridGroup(),
            'Matched Customer Group "' . $customerFixture->getGroup() . '" not found in the grid'
        );
        $fixtureName = $customerFixture->getFirstName() . " " . $customerFixture->getLastName();
        $this->assertEquals(
            $fixtureName,
            $customerGridBlock->getGridName(),
            'Matched Customer Name "' . $fixtureName . '" not found in the grid'
        );
    }
}
