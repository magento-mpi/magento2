<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\SalesRule\Test\Repository\SalesRule as Repository;

/**
 * Class BasicPromoTest
 *
 */
class BasicPromoTest extends Functional
{
    /**
     * Using customer segmentation to apply shopping cart promotion
     *
     * @ZephyrId MAGETWO-12397
     */
    public function testCustomerSegmentWithSalesRulePromotion()
    {
        // Get Fixture for this test and persist it.
        $fixture = Factory::getFixtureFactory()->getMagentoSalesRuleSalesRule();
        // All Preconditions are setup here
        $fixture->persist();
        // Precondition
        // Create the customer segment
        $customerSegmentFixture = Factory::getFixtureFactory()->getMagentoCustomerSegmentSegmentGeneralProperties();
        /* @var int $customerSegmentId */
        $customerSegmentId = Factory::getApp()->magentoCustomerSegmentCustomerSegment($customerSegmentFixture);
        $this->assertNotEmpty($customerSegmentId, 'No customer segment id returned by customer segment precondition');
        // Create Customer Segment Condition
        $customerSegmentConditionFixture = Factory::getFixtureFactory()->getMagentoCustomerSegmentSegmentConditions(
            array('segment_id' => $customerSegmentId, 'name' => $customerSegmentFixture->getSegmentName())
        );
        $customerSegmentConditionFixture->switchData('retailer_condition_curl');
        Factory::getApp()->magentoCustomerSegmentCustomerSegmentCondition($customerSegmentConditionFixture);
        // Open the backend page to create a sales rule
        $salesRulePage = Factory::getPageFactory()->getSalesRulePromoQuoteIndex();
        $salesRulePage->open();
        // Click on Plus Sign
        $salesRulePage->getGridPageActions()->addNew();
        $salesRulePageNew = Factory::getPageFactory()->getSalesRulePromoQuoteNew();
        $newSalesRuleForm = $salesRulePageNew->getSalesRuleForm();
        // Use fixture to populate
        $newSalesRuleForm->fill($fixture);
        // Setup Condition open tab
        $newSalesRuleForm->openTab('conditions');
        // Add New Condition
        $salesRulePageNew->getConditionsTab()->addCustomerSegmentCondition($fixture, $customerSegmentId);
        // Setup Discount
        $newSalesRuleForm->openTab('actions');
        $conditionsFixture = Factory::getFixtureFactory()->getMagentoSalesRuleSalesRule();
        $conditionsFixture->switchData(Repository::ACTIONS);
        $salesRulePageNew->getSalesRuleForm()->fill($conditionsFixture);
        $salesRulePageNew->getFormPageActions()->save();
        // Verify success message
        $this->assertContains(
            'The rule has been saved.',
            $salesRulePageNew->getMessagesBlock()->getSuccessMessages(),
            'Cart Price Rule Not Saved!'
        );
        // Verify it is in the grid
        // No need to go to the grid as the success page of adding a new rule is the grid
        $gridBlock = $salesRulePage->getPromoQuoteGrid();
        $salesRuleId = $gridBlock->getIdOfRow(array('name' => $fixture->getSalesRuleName()));
        // Sanity check that we have an id of the cart price rule that was created, we need it for delete later.
        $this->assertNotEmpty(
            $salesRuleId,
            'Cart Rule Price Name "' . $fixture->getSalesRuleName() . '" not found in the grid'
        );
        // Save id for use later
        $fixture->setSalesRuleId($salesRuleId);
        // Login on front end as customer
        $customerAccountLoginPage = Factory::getPageFactory()->getCustomerAccountLogin();
        $customerAccountLoginPage->open();
        $loginBlock = $customerAccountLoginPage->getLoginBlock();
        $loginBlock->login($fixture->getCustomerFixture());
        // Go to product page
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $url = $_ENV['app_frontend_url'] . $fixture->getProductFixture()->getUrlKey() . '.html';
        Factory::getClientBrowser()->open($url);
        // Add product to cart
        $productPage->getViewBlock()->addToCart($fixture->getProductFixture());
        // Open Cart
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCartIndex();
        $checkoutCartPage->open();
        // Verify correct discount applied
        $discount = $checkoutCartPage->getCartBlock()->getDiscountTotal();
        // Calculate Discounted Price expected
        $expectedDiscount = sprintf('-$%.2f', $fixture->getProductPrice() * ($conditionsFixture->getDiscount() / 100));
        $this->assertEquals($expectedDiscount, $discount, "Discount was not correctly applied");
        // Clean up after test
        Factory::getApp()->magentoSalesRuleDeleteSalesRule($fixture);
    }
}
