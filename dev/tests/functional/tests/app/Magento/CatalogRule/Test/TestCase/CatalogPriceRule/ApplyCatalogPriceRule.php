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

use Magento\Catalog\Test\Fixture;
use Magento\Catalog\Test\Fixture\ConfigurableProduct;
use Magento\Catalog\Test\Fixture\Product;
use Magento\Catalog\Test\Repository\ConfigurableProduct as Repository;
use Magento\Catalog\Test\Repository\SimpleProduct;
use Magento\Checkout\Test\Fixture\CheckMoneyOrderFlat;
use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;

/**
 * Class ApplyCatalogPriceRule
 *
 * @package Magento\CatalogRule\Test\TestCase\CatalogPriceRule
 */
class ApplyCatalogPriceRule extends Functional
{
    private $discountRate;

    /**
     * Apply Catalog Price Rule to Products
     *
     * @ZephyrId MAGETWO-12389
     */
    public function testApplyCatalogPriceRule()
    {
        // Create Simple Product
        $simple = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct(
            array(Fixture\SimpleProduct::PRICE_VALUE => 11)
        );
        $simple->switchData(SimpleProduct::NEW_CATEGORY);
        $simple->persist();

        // Create Configurable Product with same category
        $configurable = Factory::getFixtureFactory()->getMagentoCatalogConfigurableProduct(
            array('categories' => $simple->getCategories())
        );
        $configurable->switchData(Repository::CONFIGURABLE);
        $configurable->persist();

        $products = array($simple, $configurable);

        // Create Customer
        $customer = Factory::getFixtureFactory()->getMagentoCustomerCustomer();
        $customer->switchData('customer_US_1');
        $customer->persist();

        // Create Banner
        $banner = Factory::getFixtureFactory()->getMagentoBannerBanner();
        $banner->persist();

        // Create Frontend App
        $frontendApp = Factory::getFixtureFactory()->getMagentoWidgetBannerRotatorWidget();
        $frontendApp->persist();

        // Create new Catalog Price Rule
        $categoryIds = $configurable->getCategoryIds();
        $catalogPriceRuleId = $this->createNewCatalogPriceRule($categoryIds[0]);

        // Update Banner with related Catalog Price Rule
        $banner->relateCatalogPriceRule($catalogPriceRuleId);
        $banner->persist();

        // Verify applied catalog price rules
        $this->verifyPriceRules($products);
    }

    /**
     * Create and Apply new Catalog Price Rule
     * @param string $categoryId
     * @return string $catalogPriceRuleId
     */
    public function createNewCatalogPriceRule($categoryId)
    {
        // Admin login
        Factory::getApp()->magentoBackendLoginUser();

        // Open Catalog Price Rule page
        $catalogRulePage = Factory::getPageFactory()->getCatalogRulePromoCatalog();
        $catalogRulePage->open();

        // Add new Catalog Price Rule
        $catalogRuleGrid = $catalogRulePage->getCatalogPriceRuleGridBlock();
        $catalogRuleGrid->addNewCatalogRule();

        // Fill and Save the Form
        $catalogRuleCreatePage = Factory::getPageFactory()->getCatalogRulePromoCatalogNew();
        $newCatalogRuleForm = $catalogRuleCreatePage->getCatalogPriceRuleForm();
        $catalogRuleFixture = Factory::getFixtureFactory()->getMagentoCatalogRuleCatalogPriceRule(
            array('category_id' => $categoryId)
        );
        $newCatalogRuleForm->fill($catalogRuleFixture);
        $newCatalogRuleForm->save();

        // Save fixture discount rate
        $this->discountRate = $catalogRuleFixture->getDiscountAmount() * .01;

        // Verify Success Message
        $messagesBlock = $catalogRulePage->getMessagesBlock();
        $messagesBlock->assertSuccessMessage();

        // Verify Attention/Notice Message
        $messagesBlock->assertNoticeMessage();

        // Verify Catalog Price Rule in grid
        $catalogRulePage->open();
        $gridBlock = $catalogRulePage->getCatalogPriceRuleGridBlock();
        $this->assertTrue(
            $gridBlock->isRuleVisible($catalogRuleFixture->getRuleName()),
            'Rule name "' . $catalogRuleFixture->getRuleName() . '" not found in the grid'
        );
        // Get the Id
        $catalogPriceRuleId = $gridBlock->getCatalogPriceId($catalogRuleFixture->getRuleName());

        // Apply Catalog Price Rule
        $gridBlock->applyRules();

        // Verify Success Message
        $messagesBlock = $catalogRulePage->getMessagesBlock();
        $messagesBlock->assertSuccessMessage();

        // Return Catalog Price Rule Id
        return $catalogPriceRuleId;
    }

    /**
     * Add products to cart
     * @param Product[] $products
     */
    protected function verifyAddProducts(array $products)
    {
        // Get empty cart
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartPage->open();
        $checkoutCartPage->getCartBlock()->clearShoppingCart();

        foreach ($products as $product) {
            // Open Product page
            $productPage = Factory::getPageFactory()->getCatalogProductView();
            $productPage->init($product);
            $productPage->open();
            $productViewBlock = $productPage->getViewBlock();

            // Verify Product page price
            $appliedRulePrice = $product->getProductPrice() * $this->discountRate;
            if ($product instanceof ConfigurableProduct) {
                // Select option
                $productViewBlock->fillOptions($product);
                $appliedRulePrice += $product->getProductOptionsPrice();
            }
            $this->assertContains((string)$appliedRulePrice, $productViewBlock->getProductSpecialPrice());

            // Add to Cart
            $productViewBlock->addToCart($product);
            $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
            $checkoutCartPage->getMessageBlock()->assertSuccessMessage();

            // Verify Cart page price
            $unitPrice = $checkoutCartPage->getCartBlock()->getCartItemUnitPrice($product);
            $this->assertContains(
                (string)$appliedRulePrice,
                $unitPrice,
                'Incorrect price for ' . $product->getProductName()
            );
        }
    }

    /**
     * Process Magento Checkout
     * @param CheckMoneyOrderFlat $fixture
     */
    protected function checkoutProcess(CheckMoneyOrderFlat $fixture)
    {
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartPage->getCartBlock()->getOnepageLinkBlock()->proceedToCheckout();

        //Proceed Checkout
        $checkoutOnePage = Factory::getPageFactory()->getCheckoutOnepage();
        $checkoutOnePage->getLoginBlock()->checkoutMethod($fixture);
        $checkoutOnePage->getBillingBlock()->fillBilling($fixture);
        $checkoutOnePage->getShippingMethodBlock()->selectShippingMethod($fixture);
        $checkoutOnePage->getPaymentMethodsBlock()->selectPaymentMethod($fixture);
        $reviewBlock = $checkoutOnePage->getReviewBlock();

        $this->assertContains($fixture->getGrandTotal(), $reviewBlock->getGrandTotal(), 'Incorrect Grand Total');
        $reviewBlock->placeOrder();
    }

    /**
     * This method verifies information on the storefront.
     * @param Product[] $products
     */
    protected function verifyPriceRules(array $products)
    {
        // Verify category page prices
        $this->verifyCategoryPrices($products);

        // Verify product and cart page prices
        $this->verifyAddProducts($products);

        // Verify one page checkout prices
        $fixture = Factory::getFixtureFactory()->getMagentoCheckoutCheckMoneyOrderFlat(
            array('products' => $products)
        );
        $fixture->persist();
        $this->checkoutProcess($fixture);

        //Verify order Id available
        $successPage = Factory::getPageFactory()->getCheckoutOnepageSuccess();
        $this->assertNotEmpty($successPage->getSuccessBlock()->getOrderId($fixture));
    }

    /**
     * This method verifies special prices on the category page.
     *
     * @param Product[] $products
     */
    protected function verifyCategoryPrices(array $products)
    {
        // open the front end home page of the store
        $frontendHomePage = Factory::getPageFactory()->getCmsIndexIndex();
        $frontendHomePage->open();
        // open the category associated with the product
        $frontendHomePage->getTopmenu()->selectCategoryByName($products[0]->getCategoryName());
        // verify the product is displayed in the category
        $categoryPage = Factory::getPageFactory()->getCatalogCategoryView();
        $productListBlock = $categoryPage->getListProductBlock();
        foreach ($products as $product) {
            $this->assertTrue($productListBlock->isProductVisible($product->getProductName()));
            $productPriceBlock = $productListBlock->getProductPriceBlock($product->getProductName());
            $this->assertContains(
                (string)($product->getProductPrice() * $this->discountRate),
                $productPriceBlock->getEffectivePrice(),
                'Displayed price does not match expected price.'
            );
        }
    }
}
