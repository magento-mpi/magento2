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
namespace Magento\SalesRule\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\SalesRule\Test\Page\SalesRuleNew;
use Magento\SalesRule\Test\Repository\SalesRule as Repository;

class BasicPromoTest extends Functional
{
    const CUSTOMER_SEGMENT = 'Customer Segment';

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
        // Open the backend page to create a sales rule
        $salesRulePage = Factory::getPageFactory()->getSalesRulePromoQuote();
        $salesRulePage->open();
        // Click on Plus Sign
        $salesRulePage->clickAddNew();
        $salesRulePageNew = Factory::getPageFactory()->getSalesRulePromoQuoteNew();
        $newSalesRuleForm = $salesRulePageNew->getPromoQuoteForm();
        // Use fixture to populate
        $newSalesRuleForm->fill($fixture);
        // Setup Condition open tab
        $salesRulePageNew->getConditionsFormTab()->openTab(SalesRuleNew::CONDITIONS_TAB_SELECTOR);
        // Add new condition
        $salesRulePageNew->getConditionsActions()->clickAddNew();
        // Select Customer Segment
        $salesRulePageNew->getConditionsActions()->selectCondition(self::CUSTOMER_SEGMENT);
        // Click ellipsis
        $salesRulePageNew->getConditionsActions()->clickEllipsis();
        // Set Customer Segment Id
        $salesRulePageNew->getConditionsActions()->selectConditionValue($fixture->getCustomerSegmentId());
        // Apply change
        $salesRulePageNew->getConditionsActions()->clickApply();
        // Setup Discount
        $salesRulePageNew->getActionsFormTab()->openTab(SalesRuleNew::ACTIONS_TAB_SELECTOR);
        $conditionsFixture = Factory::getFixtureFactory()->getMagentoSalesRuleSalesRule();
        $conditionsFixture->switchData(Repository::ACTIONS);
        $salesRulePageNew->getPromoQuoteForm()->fill($conditionsFixture);
        // Save new rule
        $newSalesRuleForm->clickSave();
        // Verify success message
        $this->assertContains(
            'The rule has been saved.',
            $salesRulePageNew->getMessageBlock()->getSuccessMessages(),
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
        // Login on front end as customer
        $customerAccountLoginPage = Factory::getPageFactory()->getCustomerAccountLogin();
        $customerAccountLoginPage->open();
        $loginBlock = $customerAccountLoginPage->getLoginBlock();
        $loginBlock->login($fixture->getCustomerFixture());
        //Ensure shopping cart is empty
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartPage->open();
        $checkoutCartPage->getCartBlock()->clearShoppingCart();
        // Go to product page
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $productPage->init($fixture->getProductFixture());
        $productPage->open();
        // Add product to cart
        $productPage->getViewBlock()->addToCart($fixture->getProductFixture());
        // Open Cart
        $checkoutCartPage->open();
        // Verify correct discount applied
        $discount = $checkoutCartPage->getCartBlock()->getDiscountTotal();
        // Calculate Discounted Price expected
        $expectedDiscount = sprintf('-$%.2f', $fixture->getProductPrice() * ($conditionsFixture->getDiscount() / 100));
        $this->assertEquals($expectedDiscount, $discount, "Discount was not correctly applied");
        // Clean up after test
        $deleteSalesRuleFixture = Factory::getFixtureFactory()->getMagentoSalesRuleDeleteSalesRule();
        $deleteSalesRuleFixture->setSalesRuleId($salesRuleId);
        $deleteSalesRuleFixture->persist();
    }
}
