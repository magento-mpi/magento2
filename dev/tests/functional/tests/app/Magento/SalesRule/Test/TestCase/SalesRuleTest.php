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

class SalesRuleTest extends Functional
{
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
     * Setup the preconditions of this test
     */
    protected function setUp()
    {
        // Login to the backend
        Factory::getApp()->magentoBackendLoginUser();
        // Create a customer
        $this->customerFixture = Factory::getFixtureFactory()->getMagentoCustomerCustomer();
        Factory::getApp()->magentoCustomerCreateCustomer($this->customerFixture);
        // Create a product
        $this->productFixture = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        Factory::getApp()->magentoCatalogCreateProduct($this->productFixture);
        // TODO Create a customer segment
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
        // TODO Verify Cart Price Rule is applied
    }
}
