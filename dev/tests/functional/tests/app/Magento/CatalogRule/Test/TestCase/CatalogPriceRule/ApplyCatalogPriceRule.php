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
use Magento\Catalog\Test\Repository\ConfigurableProduct;
use Magento\Catalog\Test\Repository\SimpleProduct;
use Magento\Checkout\Test\Fixture\Checkout;
use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Mtf\Client\Element\Locator;
use Magento\Catalog\Test\Fixture\Product;

/**
 * Class ApplyCatalogPriceRule
 *
 * @package Magento\CatalogRule\Test\TestCase\CatalogPriceRule
 */
class ApplyCatalogPriceRule extends Functional
{
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
        $configurable->switchData(ConfigurableProduct::CONFIGURABLE);
        $configurable->persist();

        /** @var Product[] */
        $products = array($simple, $configurable);

        // Create Customer
        $customer = Factory::getFixtureFactory()->getMagentoCustomerCustomer();
        $customer->switchData('customer_US_1');
        $customer->persist();

        // Create Banner
        $banner = Factory::getFixtureFactory()->getMagentoBannerBanner();
        $banner->persist();

        // Create Frontend App
        $frontendApp = Factory::getFixtureFactory()->getMagentoWidgetInstance();
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

        // Verify Success Message
        $messagesBlock = $catalogRulePage->getMessagesBlock();
        $messagesBlock->assertSuccessMessage();

        // Verify Attention/Notice Message
        $messagesBlock->assertNoticeMessage();

        // Verify Catalog Price Rule in grid
        $catalogRulePage->open();
        $gridBlock = $catalogRulePage->getCatalogPriceRuleGridBlock();
        $gridRow = $gridBlock->getRow(array('name' => $catalogRuleFixture->getRuleName()));
        $this->assertTrue(
            $gridRow->isVisible(),
            'Rule name "' . $catalogRuleFixture->getRuleName() . '" not found in the grid'
        );
        // Get the Id
        $catalogPriceRuleId = $gridRow->find('//td[@data-column="rule_id"]', Locator::SELECTOR_XPATH)->getText();

        // Apply Catalog Price Rule
        $catalogRulePage->applyRules();

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
            $productPage = Factory::getPageFactory()->getCatalogProductView();
            $productPage->init($product);
            $productPage->open();
            $productViewBlock = $productPage->getViewBlock();
            $appliedRulePrice = (string)($product->getProductPrice() * .5);
            $this->assertContains($appliedRulePrice, $productViewBlock->getProductSpecialPrice());
            $productViewBlock->addToCart($product);
            Factory::getPageFactory()->getCheckoutCart()->getMessageBlock()->assertSuccessMessage();
        }
        // todo cart special price check
    }

    /**
     * Process Magento Checkout
     * @param Checkout $fixture
     */
    protected function checkoutProcess(Checkout $fixture)
    {
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartPage->getCartBlock()->getOnepageLinkBlock()->proceedToCheckout();

        //Proceed Checkout
        /** @var \Magento\Checkout\Test\Page\CheckoutOnepage $checkoutOnePage */
        $checkoutOnePage = Factory::getPageFactory()->getCheckoutOnepage();
        $checkoutOnePage->getLoginBlock()->checkoutMethod($fixture);
        $checkoutOnePage->getBillingBlock()->fillBilling($fixture);
        $checkoutOnePage->getShippingMethodBlock()->selectShippingMethod($fixture);
        $checkoutOnePage->getPaymentMethodsBlock()->selectPaymentMethod($fixture);
        // todo one page checkout special price check
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

        $fixture = Factory::getFixtureFactory()->getMagentoCheckoutCheckMoneyOrder(
            array('products' => $products)
        );
        $fixture->persist();
        // Verify one page checkout prices
        $this->checkoutProcess($fixture);
    }

    /**
     * This method verifies special prices on the category page.
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
            $this->assertContains(
                (string)($product->getProductPrice() * .5),
                $productListBlock->getProductSpecialPrice($product->getProductName()),
                'Displayed price does not match expected price.'
            );
        }
    }
}
