<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\TestCase\CatalogPriceRule;

use Magento\Catalog\Test\Fixture\Product;
use Magento\Catalog\Test\Repository\SimpleProduct;
use Magento\Customer\Test\Fixture\Customer;
use Magento\CatalogRule\Test\Fixture\CatalogPriceRule;
use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;

/**
 * Class ApplyCustomerGroupCatalogRule
 */
class ApplyCustomerGroupCatalogRuleTest extends Functional
{
    /**
     *  Variable for discount amount converted to decimal form
     *
     * @var float
     */
    protected $discountDecimal;

    /**
     * Fixture of catalog price rule
     *
     * @var CatalogPriceRule
     */
    protected $catalogRule;

    /**
     * Applying Catalog Price Rules to specific customer group
     *
     * @ZephyrId MAGETWO-12908
     */
    public function testApplyCustomerGroupCatalogRule()
    {
        // Create Customer
        $customerFixture = Factory::getFixtureFactory()->getMagentoCustomerCustomer();
        $customerFixture->switchData('backend_customer');
        $customerFixture->persist();
        // Create Customer Group
        $customerGroupFixture = Factory::getFixtureFactory()->getMagentoCustomerCustomerGroup();
        $customerGroupFixture->persist();
        $groupName = $customerGroupFixture->getGroupName();
        $groupId = $customerGroupFixture->getGroupId();
        // update the customer fixture with the newly added customer group
        $customerFixture->updateCustomerGroup($groupName, $groupId);
        // Create Simple Product
        $simpleProductFixture = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct();
        $simpleProductFixture->switchData(SimpleProduct::NEW_CATEGORY);

        $categoryIds = $simpleProductFixture->getCategoryIds();
        // Create Customer Group Catalog Price Rule
        // Admin login
        Factory::getApp()->magentoBackendLoginUser();

        // Customer needs to be in a group and front end customer creation doesn't set group
        $customerGridPage = Factory::getPageFactory()->getCustomerIndex();
        // Edit Customer just created
        $customerGridPage->open();
        $customerGrid = $customerGridPage->getCustomerGridBlock();
        $customerGrid->searchAndOpen(['email' => $customerFixture->getEmail()]);
        $customerEditPage = Factory::getPageFactory()->getCustomerIndexEdit();
        $editCustomerForm = $customerEditPage->getCustomerForm();
        // Set group to Retailer
        $editCustomerForm->openTab('account_information');
        $editCustomerForm->fillCustomer($customerFixture);
        // Save Customer Edit
        $customerEditPage->getPageActionsBlock()->save();

        // Add Customer Group Catalog Price Rule
        $catalogRulePage = Factory::getPageFactory()->getCatalogRulePromoCatalogIndex();
        $catalogRulePage->open();
        $catalogRuleGrid = $catalogRulePage->getGridPageActions();
        $catalogRuleGrid->addNew();

        // Fill and Save the Form
        $catalogRuleCreatePage = Factory::getPageFactory()->getCatalogRulePromoCatalogNew();
        $newCatalogRuleForm = $catalogRuleCreatePage->getEditForm();
        $catalogRuleFixture = Factory::getFixtureFactory()->getMagentoCatalogRuleCatalogPriceRule(
            [
                'category_id' => $categoryIds[0],
                'group_value' => $groupName,
                'group_id' => $groupId
            ]
        );
        // prepare data for tear down
        $this->catalogRule = $catalogRuleFixture;
        // convert the discount amount to a decimal form
        $this->discountDecimal = $catalogRuleFixture->getDiscountAmount() * .01;
        $newCatalogRuleForm->fill($catalogRuleFixture);
        $catalogRuleCreatePage->getFormPageActions()->save();

        // Verify Success Message
        $messagesBlock = $catalogRulePage->getMessagesBlock();
        $messagesBlock->waitSuccessMessage();

        // Verify Notice Message
        $messagesBlock->assertNoticeMessage();

        // Save new product
        $simpleProductFixture->persist();

        // Apply Catalog Price Rule
        $catalogRulePage->open();
        $catalogRuleGrid = $catalogRulePage->getGridPageActions();
        $catalogRuleGrid->applyRules();

        // Verify Success Message
        $catalogRulePage->getMessagesBlock()->waitSuccessMessage();

        $this->verifyGuestPrice($simpleProductFixture);
        $this->verifyCustomerPrice($simpleProductFixture, $customerFixture);
    }

    /**
     * This method verifies guest price information on the storefront.
     *
     * @param Product $product
     * @return void
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
        $productPriceBlock = $productListBlock->getProductPriceBlock($product->getName());
        // verify the special price is not applied
        $this->assertFalse($productPriceBlock->isSpecialPriceVisible(), 'Special price is visible and not expected.');
        $this->assertContains(
            $product->getProductPrice(),
            $productPriceBlock->getEffectivePrice(),
            'Displayed price does not match expected price.'
        );
        // Verify product detail
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        Factory::getClientBrowser()->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $productViewBlock = $productPage->getViewBlock();
        $productPriceBlock = $productViewBlock->getPriceBlock();
        // verify special price is not applied
        $this->assertFalse($productPriceBlock->isSpecialPriceVisible(), 'Special price is visible adn not expected.');
        $this->assertContains($product->getProductPrice(), $productPriceBlock->getEffectivePrice());
        // Verify price in the cart
        $productViewBlock->addToCart($product);
        Factory::getPageFactory()->getCheckoutCartIndex()->getMessagesBlock()->waitSuccessMessage();
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCartIndex();
        $unitPrice = $checkoutCartPage->getCartBlock()->getCartItem($product)->getPrice();
        $this->assertContains(
            $product->getProductPrice(),
            (string)$unitPrice,
            'Displayed price is not the expected price'
        );
        $checkoutCartPage->getCartBlock()->clearShoppingCart();
    }

    /**
     * This method verifies customer price information on the storefront.
     *
     * @param Product $product
     * @param Customer $customer
     * @return void
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
        $this->assertTrue($productListBlock->isProductVisible($product->getName()));
        $productPriceBlock = $productListBlock->getProductPriceBlock($product->getName());
        $this->assertContains(
            (string)($product->getProductPrice() * $this->discountDecimal),
            $productPriceBlock->getSpecialPrice(),
            'Displayed special price does not match expected price.'
        );
        $this->assertContains(
            $product->getProductPrice(),
            $productPriceBlock->getRegularPrice(),
            'Displayed regular price does not match expected price.'
        );
        // Verify product and cart page prices
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCartIndex();
        $checkoutCartPage->open();
        $checkoutCartPage->getCartBlock()->clearShoppingCart();
        // Verify category detail page price
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        Factory::getClientBrowser()->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $productViewBlock = $productPage->getViewBlock();
        $productPriceBlock = $productViewBlock->getPriceBlock();
        $this->assertContains(
            (string)($product->getProductPrice() * $this->discountDecimal),
            $productPriceBlock->getSpecialPrice()
        );
        $this->assertContains($product->getProductPrice(), $productPriceBlock->getRegularPrice());
        $productViewBlock->addToCart($product);
        Factory::getPageFactory()->getCheckoutCartIndex()->getMessagesBlock()->waitSuccessMessage();
        // Verify price in the cart
        $this->assertContains(
            (string)($product->getProductPrice() * $this->discountDecimal),
            (string)$checkoutCartPage->getCartBlock()->getCartItem($product)->getPrice(),
            "Discount was not correctly applied"
        );
    }

    /**
     * Clear data after test
     *
     * @return void
     */
    public function tearDown()
    {
        if (!$this->catalogRule) {
            return;
        }

        // Open Catalog Price Rule page
        $catalogRulePage = Factory::getPageFactory()->getCatalogRulePromoCatalogIndex();
        $catalogRulePage->open();
        $catalogRulePage->getCatalogRuleGrid()->searchAndOpen(['name' => $this->catalogRule->getRuleName()]);

        // Delete Catalog Price Rule
        $catalogRuleCreatePage = Factory::getPageFactory()->getCatalogRulePromoCatalogNew();
        $catalogRuleCreatePage->getFormPageActions()->delete();
    }
}
