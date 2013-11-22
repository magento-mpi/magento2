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
use Magento\Customer\Test\Fixture\Customer;
use Magento\Catalog\Test\Fixture\Product;
use Magento\CustomerSegment\Test\Fixture\CustomerSegment;
use Magento\CustomerSegment\Test\Fixture\SegmentCondition;
use Magento\SalesRule\Test\Page\SalesRuleNew;
use Magento\SalesRule\Test\Repository\SalesRule as Repository;

class SalesRuleTest extends Functional
{
    const CUSTOMER_SEGMENT = 'Customer Segment';
    /**
     * @var Customer
     */
    protected $customerFixture;
    /**
     * @var Product
     */
    protected $productFixture;
    /**
     * @var CustomerSegment
     */
    protected $customerSegment;

    /**
     * @var int
     */
    protected $customerSegmentId;

    /**
     * @var SegmentCondition
     */
    protected $customerSegmentFixture;

    /**
     * Setup the preconditions of this test
     */
    protected function setUp()
    {
        // Login to the backend
        Factory::getApp()->magentoBackendLoginUser();
        // Create a customer
        $this->customerFixture = Factory::getFixtureFactory()->getMagentoCustomerCustomer();
        $this->customerFixture->switchData('backend_retailer_customer');
        Factory::getApp()->magentoCustomerCreateCustomer($this->customerFixture);
        // Customer needs to be in a group and front end customer creation doesn't set group
        $customerGridPage = Factory::getPageFactory()->getCustomer();
        $customerEditPage = Factory::getPageFactory()->getCustomerEdit();
        $customerGrid = $customerGridPage->getCustomerGridBlock();
        // Edit Customer just created
        $customerGridPage->open();
        $customerGrid->searchAndOpen([
            'email' => $this->customerFixture->getEmail()
        ]);
        $editCustomerForm = $customerEditPage->getEditCustomerForm();
        // Set group to Retailer
        $editCustomerForm->openTab('customer_info_tabs_account');
        $editCustomerForm->fill($this->customerFixture);
        // Save Customer Edit
        $editCustomerForm->save();
        // Create a product
        $this->productFixture = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        Factory::getApp()->magentoCatalogCreateProduct($this->productFixture);
        // Create the customer segment
        $this->customerSegmentFixture = Factory::getFixtureFactory()->getMagentoCustomerSegmentCustomerSegment();
        $this->customerSegmentId = Factory::getApp()->magentoCustomerSegmentCustomerSegment($this->customerSegmentFixture);
        // Create Customer Segment Condition
        $customerSegmentConditionFixture = Factory::getFixtureFactory()->getMagentoCustomerSegmentSegmentCondition();
        $customerSegmentConditionFixture->setPlaceHolders(
            [
                'segment_id' => $this->customerSegmentId,
                'name' => $this->customerSegmentFixture->getSegmentName()
            ]
        );
        $customerSegmentConditionFixture->switchData('retailer_condition_curl');
        Factory::getApp()->magentoCustomerSegmentCustomerSegmentCondition($customerSegmentConditionFixture);
    }

    /**
     * Using customer segmentation to apply shopping cart promotion
     *
     * @ZephyrId MAGETWO-12397
     */
    public function testCustomerSegmentWithSalesRulePromotion()
    {
        $fixture = Factory::getFixtureFactory()->getMagentoSalesRuleSalesRule();
        // Open the backend page to create a sales rule
        $salesRulePage = Factory::getPageFactory()->getSalesRulePromoQuote();
        $salesRulePage->open();
        $this->assertAttributeInstanceOf(
            'Magento\SalesRule\Test\Block\PromoQuoteGrid',
            'promoQuoteGrid',
            $salesRulePage
        );
        // Click on Plus Sign
        $pageActionBlock = $salesRulePage->getPageActionsBlock();
        $pageActionBlock->clickAddNew();
        $salesRulePageNew = Factory::getPageFactory()->getSalesRulePromoQuoteNew();
        $newSalesRuleForm = $salesRulePageNew->getPromoQuoteForm();
        // Use fixture to populate
        $newSalesRuleForm->fill($fixture);
        // Setup Condition open tab
        $salesRulePageNew->getConditionsFormTab()->openTab(SalesRuleNew::CONDITIONS_TAB_ID);
        // Add new condition
        $salesRulePageNew->getConditionsActions()->clickAddNew();
        // Select Customer Segment
        $salesRulePageNew->getConditionsActions()->selectCondition(self::CUSTOMER_SEGMENT);
        // Click ellipsis
        $salesRulePageNew->getConditionsActions()->clickEllipsis();
        // Set Customer Segment Id
        $salesRulePageNew->getConditionsActions()->selectConditionValue($this->customerSegmentId);
        // Apply change
        $salesRulePageNew->getConditionsActions()->clickApply();
        // Setup Discount
        $salesRulePageNew->getActionsFormTab()->openTab(SalesRuleNew::ACTIONS_TAB_ID);
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
        $this->assertTrue($gridBlock->isRowVisible([
            'name' => $fixture->getSalesRuleName()
        ], true), 'Cart Rule Price Name "' . $fixture->getSalesRuleName() . '" not found in the grid');
        // Login on front end as customer
        $customerAccountLoginPage = Factory::getPageFactory()->getCustomerAccountLogin();
        $customerAccountLoginPage->open();
        $loginBlock = $customerAccountLoginPage->getLoginBlock();
        $loginBlock->login($this->customerFixture);
        //Ensure shopping cart is empty
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartPage->open();
        $checkoutCartPage->getCartBlock()->clearShoppingCart();
        // Go to product page
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $productPage->init($this->productFixture);
        $productPage->open();
        // Add product to cart
        $productPage->getViewBlock()->addToCart($this->productFixture);
        // Open Cart
        $checkoutCartPage->open();
        // Verify correct discount applied
        $discount = $checkoutCartPage->getCartBlock()->getDiscountTotal();
        $this->assertEquals('-$5.00',$discount,"Discount was not correctly applied");
        // TODO delete cart price rule so next run is clean
    }
}
