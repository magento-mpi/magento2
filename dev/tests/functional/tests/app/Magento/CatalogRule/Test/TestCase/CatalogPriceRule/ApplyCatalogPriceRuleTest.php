<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\TestCase\CatalogPriceRule;

use Magento\Catalog\Test\Fixture;
use Magento\ConfigurableProduct\Test\Fixture\ConfigurableProduct;
use Magento\Catalog\Test\Fixture\Product;
use Magento\ConfigurableProduct\Test\Repository\ConfigurableProduct as Repository;
use Magento\Catalog\Test\Repository\SimpleProduct;
use Magento\CatalogRule\Test\Repository\CatalogPriceRule;
use Magento\Checkout\Test\Fixture\CheckMoneyOrderFlat;
use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;

/**
 * Class ApplyCatalogPriceRule
 */
class ApplyCatalogPriceRuleTest extends Functional
{
    /**
     * This member holds the floating point version of the discount percentage.
     *
     * @var float
     */
    private $discountRate;

    /**
     * Id of catalog price rule
     *
     * @var int
     */
    protected $catalogPriceRuleId;

    /**
     * Apply Catalog Price Rule to Products
     *
     * @ZephyrId MAGETWO-12389
     */
    public function testApplyCatalogPriceRule()
    {
        // Create Simple Product
        $simple = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct();
        $simple->switchData(SimpleProduct::BASE);
        $simple->persist();

        // Create Configurable Product with same category
        $configurable = Factory::getFixtureFactory()->getMagentoConfigurableProductConfigurableProduct(
            ['categories' => $simple->getCategories()]
        );
        $configurable->switchData(Repository::CONFIGURABLE);
        $configurable->persist();

        $products = [$simple, $configurable];

        // Create Customer
        $customer = Factory::getFixtureFactory()->getMagentoCustomerCustomer();
        $customer->switchData('customer_US_1');
        $customer->persist();

        // Create Banner
        $banner = Factory::getFixtureFactory()->getMagentoBannerBanner();
        $banner->persist();

        // Create Frontend App
        $objectManager = Factory::getObjectManager();
        $frontendApp = $objectManager->create('\Magento\Banner\Test\Fixture\Widget', ['dataSet' => 'banner_rotator']);
        $frontendApp->persist();

        // Create new Catalog Price Rule
        $categoryIds = $configurable->getCategoryIds();
        $catalogPriceRuleId = $this->createNewCatalogPriceRule($categoryIds[0]);

        // Prepare data for tear down
        $this->catalogPriceRuleId = $catalogPriceRuleId;

        // Update Banner with related Catalog Price Rule
        $banner->relateCatalogPriceRule($catalogPriceRuleId);
        $banner->persist();

        //Flush cache
        $cachePage = Factory::getPageFactory()->getAdminCache();
        $cachePage->open();
        $cachePage->getActionsBlock()->flushMagentoCache();
        $cachePage->getMessagesBlock()->assertSuccessMessage();

        // Verify applied catalog price rules
        $this->verifyPriceRules($products);
    }

    /**
     * Create and Apply new Catalog Price Rule
     *
     * @param string $categoryId
     * @return string $catalogPriceRuleId
     */
    public function createNewCatalogPriceRule($categoryId)
    {
        // Admin login
        Factory::getApp()->magentoBackendLoginUser();

        // Open Catalog Price Rule page
        $catalogRulePage = Factory::getPageFactory()->getCatalogRulePromoCatalogIndex();
        $catalogRulePage->open();

        // Add new Catalog Price Rule
        $catalogRuleGrid = $catalogRulePage->getGridPageActions();
        $catalogRuleGrid->addNew();

        // Fill and Save the Form
        $catalogRuleCreatePage = Factory::getPageFactory()->getCatalogRulePromoCatalogNew();
        $newCatalogRuleForm = $catalogRuleCreatePage->getEditForm();
        $catalogRuleFixture = Factory::getFixtureFactory()->getMagentoCatalogRuleCatalogPriceRule(
            ['category_id' => $categoryId]
        );
        $catalogRuleFixture->switchData(CatalogPriceRule::CATALOG_PRICE_RULE_ALL_GROUPS);
        $newCatalogRuleForm->fill($catalogRuleFixture);
        $catalogRuleCreatePage->getFormPageActions()->save();

        // Save fixture discount rate
        $this->discountRate = $catalogRuleFixture->getDiscountAmount() * .01;

        // Verify Success Message
        $messagesBlock = $catalogRulePage->getMessagesBlock();
        $messagesBlock->assertSuccessMessage();

        // Verify Attention/Notice Message
        $messagesBlock->assertNoticeMessage();

        // Verify Catalog Price Rule in grid
        $catalogRulePage->open();
        $gridBlock = $catalogRulePage->getCatalogRuleGrid();
        $filter['name'] = $catalogRuleFixture->getRuleName();
        $this->assertTrue(
            $gridBlock->isRowVisible($filter),
            'Rule name "' . $filter['name'] . '" not found in the grid'
        );
        // Get the Id
        $catalogPriceRuleId = $gridBlock->getCatalogPriceId($catalogRuleFixture->getRuleName());

        // Apply Catalog Price Rule
        $catalogRulePage->getGridPageActions()->applyRules();

        // Verify Success Message
        $messagesBlock = $catalogRulePage->getMessagesBlock();
        $messagesBlock->assertSuccessMessage();

        // Return Catalog Price Rule Id
        return $catalogPriceRuleId;
    }

    /**
     * Add products to cart
     *
     * @param Product[] $products
     * @return void
     */
    protected function verifyAddProducts(array $products)
    {
        // Get empty cart
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCartIndex();
        $checkoutCartPage->open();
        $checkoutCartPage->getCartBlock()->clearShoppingCart();

        foreach ($products as $product) {
            // Open Product page
            $productPage = Factory::getPageFactory()->getCatalogProductView();
            Factory::getClientBrowser()->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
            $productViewBlock = $productPage->getViewBlock();

            // Verify Product page price
            $appliedRulePrice = $product->getProductPrice() * $this->discountRate;
            if ($product instanceof ConfigurableProduct) {
                // Select option
                $optionsBlock = $productPage->getViewBlock()->getCustomOptionsBlock();
                $configurableOptions = [];
                $checkoutData = [];

                foreach ($product->getConfigurableOptions() as $attributeLabel => $options) {
                    $configurableOptions[] = [
                        'type' => 'dropdown',
                        'title' => $attributeLabel,
                        'value' => $options
                    ];
                }
                foreach ($product->getCheckoutData()['options']['configurable_options'] as $checkoutOption) {
                    $checkoutData[] = [
                        'type' => $configurableOptions[$checkoutOption['title']]['type'],
                        'title' => $configurableOptions[$checkoutOption['title']]['title'],
                        'value' => $configurableOptions[$checkoutOption['title']]['value'][$checkoutOption['value']],
                    ];
                }

                $optionsBlock->fillCustomOptions($checkoutData);
                $appliedRulePrice += $product->getProductOptionsPrice();
            }
            $productPriceBlock = $productViewBlock->getPriceBlock();
            $this->assertContains((string)$appliedRulePrice, $productPriceBlock->getSpecialPrice());

            // Add to Cart
            $productViewBlock->clickAddToCart();
            $checkoutCartPage = Factory::getPageFactory()->getCheckoutCartIndex();
            $checkoutCartPage->getMessagesBlock()->assertSuccessMessage();

            // Verify Cart page price
            $unitPrice = $checkoutCartPage->getCartBlock()->getCartItem($product)->getPrice();
            $this->assertEquals(
                $appliedRulePrice,
                $unitPrice,
                'Incorrect price for ' . $product->getName()
            );
        }
    }

    /**
     * Process Magento Checkout
     *
     * @param CheckMoneyOrderFlat $fixture
     * @return void
     */
    protected function checkoutProcess(CheckMoneyOrderFlat $fixture)
    {
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCartIndex();
        $checkoutCartPage->getCartBlock()->getOnepageLinkBlock()->proceedToCheckout();

        //Proceed Checkout
        $checkoutOnePage = Factory::getPageFactory()->getCheckoutOnepage();
        $checkoutOnePage->getLoginBlock()->checkoutMethod($fixture);
        $billingAddress = $fixture->getBillingAddress();
        $checkoutOnePage->getBillingBlock()->fillBilling($billingAddress);
        $checkoutOnePage->getBillingBlock()->clickContinue();
        $shippingMethod = $fixture->getShippingMethods()->getData('fields');
        $checkoutOnePage->getShippingMethodBlock()->selectShippingMethod($shippingMethod);
        $checkoutOnePage->getShippingMethodBlock()->clickContinue();
        $payment = [
            'method' => $fixture->getPaymentMethod()->getPaymentCode(),
            'dataConfig' => $fixture->getPaymentMethod()->getDataConfig(),
            'credit_card' => $fixture->getCreditCard(),
        ];
        $checkoutOnePage->getPaymentMethodsBlock()->selectPaymentMethod($payment);
        $checkoutOnePage->getPaymentMethodsBlock()->clickContinue();
        $reviewBlock = $checkoutOnePage->getReviewBlock();

        $this->assertContains($fixture->getGrandTotal(), '$' . $reviewBlock->getGrandTotal(), 'Incorrect Grand Total');
        $reviewBlock->placeOrder();
    }

    /**
     * This method verifies information on the storefront.
     *
     * @param Product[] $products
     * @return void
     */
    protected function verifyPriceRules(array $products)
    {
        // Verify Banner on the front end store home page
        $frontendHomePage = Factory::getPageFactory()->getCmsIndexIndex();
        $frontendHomePage->open();
        $bannerBlock = $frontendHomePage->getBannersBlock();
        $this->assertNotEmpty($bannerBlock->getBannerText(), "Banner is empty.");

        // open the category associated with the product
        $frontendHomePage->getTopmenu()->selectCategoryByName($products[0]->getCategoryName());

        // Verify category page prices
        $this->verifyCategoryPrices($products);

        // Verify product and cart page prices
        $this->verifyAddProducts($products);

        // Verify one page checkout prices
        $fixture = Factory::getFixtureFactory()->getMagentoCheckoutCheckMoneyOrderFlat(['products' => $products]);
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
     * @return void
     */
    protected function verifyCategoryPrices(array $products)
    {
        // verify the product is displayed in the category
        $categoryPage = Factory::getPageFactory()->getCatalogCategoryView();
        $productListBlock = $categoryPage->getListProductBlock();
        foreach ($products as $product) {
            $this->assertTrue($productListBlock->isProductVisible($product->getName()));
            $productPriceBlock = $productListBlock->getProductPriceBlock($product->getName());
            $this->assertContains(
                (string)($product->getProductPrice() * $this->discountRate),
                $productPriceBlock->getEffectivePrice(),
                'Displayed price does not match expected price.'
            );
        }
    }

    /**
     * Clear data after test
     *
     * @return void
     */
    public function tearDown()
    {
        if (!$this->catalogPriceRuleId) {
            return;
        }

        // Open Catalog Price Rule page
        $catalogRulePage = Factory::getPageFactory()->getCatalogRulePromoCatalogIndex();
        $catalogRulePage->open();
        $catalogRulePage->getCatalogRuleGrid()->searchAndOpen(['rule_id' => $this->catalogPriceRuleId]);

        // Delete Catalog Price Rule
        $catalogRuleCreatePage = Factory::getPageFactory()->getCatalogRulePromoCatalogNew();
        $catalogRuleCreatePage->getFormPageActions()->delete();
    }
}
