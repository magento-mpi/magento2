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
namespace Magento\CatalogRule\Test\TestCase\CatalogPriceRule;

use Magento\Catalog\Test\Fixture\Product;
use Magento\Catalog\Test\Repository\SimpleProduct;
use Magento\Customer\Test\Fixture\Customer;
use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Mtf\Client\Element\Locator;

/**
 * Class ApplyCustomerGroupCatalogRule
 *
 * @package Magento\CatalogRule\Test\TestCase\CatalogPriceRule
 */
class ApplyCustomerGroupCatalogRule extends Functional
{
    /**
     * Applying Catalog Price Rules to specific customer group
     *
     * @ZephyrId MAGETWO-12908
     */
    public function testApplyCustomerGroupCatalogRule()
    {
        // Create Simple Product
        $simpleProductFixture = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct();
        $simpleProductFixture->switchData(SimpleProduct::NEW_CATEGORY);
        $simpleProductFixture->persist();
        $categoryIds = $simpleProductFixture->getCategoryIds();
        // Create Customer
        $customerFixture = Factory::getFixtureFactory()->getMagentoCustomerCustomer();
        $customerFixture->switchData('customer_US_1');
        $customerFixture->persist();

        // Create Customer Group Catalog Price Rule
        // Admin login
        Factory::getApp()->magentoBackendLoginUser();

        // Add Customer Group Catalog Price Rule
        $catalogRulePage = Factory::getPageFactory()->getCatalogRulePromoCatalog();
        $catalogRulePage->open();
        $catalogRuleGrid = $catalogRulePage->getCatalogPriceRuleGridBlock();
        $catalogRuleGrid->addNewCatalogRule();

        // Fill and Save the Form
        $catalogRuleCreatePage = Factory::getPageFactory()->getCatalogRulePromoCatalogNew();
        $newCatalogRuleForm = $catalogRuleCreatePage->getCatalogPriceRuleForm();
        $catalogRuleFixture = Factory::getFixtureFactory()->getMagentoCatalogRuleCatalogPriceRule();
        $catalogRuleFixture->setPlaceHolders(array('category_id' => $categoryIds[0]));
        $catalogRuleFixture->switchData('customer_group_catalog_rule');
        $newCatalogRuleForm->fill($catalogRuleFixture);
        $newCatalogRuleForm->save();

        // Verify Success Message
        $messagesBlock = $catalogRulePage->getMessagesBlock();
        $messagesBlock->assertSuccessMessage();

        // Verify Notice Message
        $messagesBlock->assertNoticeMessage();

        // Verify Catalog Price Rule in grid
        $catalogRulePage->open();
        $gridBlock = $catalogRulePage->getCatalogPriceRuleGridBlock();
        $gridRow = $gridBlock->getRow(array('name' => $catalogRuleFixture->getRuleName()));
        $this->assertTrue(
            $gridRow->isVisible(),
            'Rule name "' . $catalogRuleFixture->getRuleName() . '" not found in the grid'
        );

        // Apply Catalog Price Rule
        $catalogRulePage->applyRules();

        // Verify Success Message
        $messagesBlock = $catalogRulePage->getMessagesBlock();
        $messagesBlock->assertSuccessMessage();

        $this->verifyGuestPrice($simpleProductFixture);
        $this->verifyCustomerPrice($simpleProductFixture, $customerFixture);
    }

    /**
     * This method verifies guest price information on the storefront.
     * @param Product $product
     */
    protected function verifyGuestPrice($product)
    {
        // Verify frontend category page prices
        $frontendHomePage = Factory::getPageFactory()->getCmsIndexIndex();
        $frontendHomePage->open();
        // open the category associated with the price rule
        $frontendHomePage->getTopmenu()->selectCategoryByName($product->getCategoryName());
        $categoryPage = Factory::getPageFactory()->getCatalogCategoryView();
        // verify price in catalog list
        $productListBlock = $categoryPage->getListProductBlock();
        $this->assertTrue($productListBlock->isProductVisible($product->getProductName()));
        $this->assertContains(
            $product->getProductPrice(),
            $productListBlock->getProductPrice($product->getProductName()),
            'Displayed price does not match expected price.'
        );
        // Verify product and cart page prices
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartPage->open();
        $checkoutCartPage->getCartBlock()->clearShoppingCart();
        // Verify product detail
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $productPage->init($product);
        $productPage->open();
        $productViewBlock = $productPage->getViewBlock();
        $appliedRulePrice = $product->getProductPrice();
        $this->assertContains($appliedRulePrice, $productViewBlock->getProductPrice());
        // Verify price in the cart
        $productViewBlock->addToCart($product);
        Factory::getPageFactory()->getCheckoutCart()->getMessageBlock()->assertSuccessMessage();
        $unitPrice = $checkoutCartPage->getCartBlock()->getCartItemUnitPrice($product);
        $this->assertContains($product->getProductPrice(), $unitPrice, "Discount was not correctly applied");
        $checkoutCartPage->getCartBlock()->clearShoppingCart();
    }

    /**
     * This method verifies customer price information on the storefront.
     * @param Product $product
     * @param Customer $customer
     */
    protected function verifyCustomerPrice($product, $customer)
    {
        // Login on front end as customer
        $customerAccountLoginPage = Factory::getPageFactory()->getCustomerAccountLogin();
        $customerAccountLoginPage->open();
        $loginBlock = $customerAccountLoginPage->getLoginBlock();
        $loginBlock->login($customer);
        // Verify category list page price
        $frontendHomePage = Factory::getPageFactory()->getCmsIndexIndex();
        $frontendHomePage->open();
        // open the category associated with the price rule
        $frontendHomePage->getTopmenu()->selectCategoryByName($product->getCategoryName());
        $categoryPage = Factory::getPageFactory()->getCatalogCategoryView();
        $productListBlock = $categoryPage->getListProductBlock();
        $this->assertTrue($productListBlock->isProductVisible($product->getProductName()));
        $this->assertContains(
            (string)($product->getProductPrice() * .5),
            $productListBlock->getProductSpecialPrice($product->getProductName()),
            'Displayed price does not match expected price.'
        );
        $this->assertContains(
            $product->getProductPrice(),
            $productListBlock->getProductPrice($product->getProductName()),
            'Displayed price does not match expected price.'
        );
        // Verify product and cart page prices
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartPage->open();
        $checkoutCartPage->getCartBlock()->clearShoppingCart();
        // Verify category detail page price
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $productPage->init($product);
        $productPage->open();
        $productViewBlock = $productPage->getViewBlock();
        $this->assertContains((string)($product->getProductPrice() * .5), $productViewBlock->getProductSpecialPrice());
        $this->assertContains($product->getProductPrice(), $productViewBlock->getProductPrice());
        $productViewBlock->addToCart($product);
        Factory::getPageFactory()->getCheckoutCart()->getMessageBlock()->assertSuccessMessage();
        // Verify price in the cart
        $this->assertContains(
            (string)($product->getProductPrice() * .5),
            $checkoutCartPage->getCartBlock()->getCartItemUnitPrice($product),
            "Discount was not correctly applied"
        );
    }
}
