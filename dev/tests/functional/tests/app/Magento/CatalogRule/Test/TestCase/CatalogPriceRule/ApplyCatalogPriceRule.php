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
use Magento\Catalog\Test\Repository\Product;
use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Mtf\Client\Element\Locator;

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
        $simple = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $simple->switchData(Product::PRODUCT_SIMPLE);
        $simple->persist();

        // Create Configurable Product
        $configurable = Factory::getFixtureFactory()->getMagentoCatalogConfigurableProduct();
        $configurable->switchData('configurable');
        $configurable->persist();

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
        $catalogPriceRuleId = $this->createNewCatalogPriceRule();

        // Update Banner with related Catalog Price Rule
        $banner->relateCatalogPriceRule($catalogPriceRuleId);
        $banner->persist();

        // Verify applied catalog price rules
        $this->verifyPriceRules($simple);
    }

    /**
     * Create and Apply new Catalog Price Rule
     */
    public function createNewCatalogPriceRule()
    {
        // Admin login
        Factory::getApp()->magentoBackendLoginUser();

        // Open Catalog Price Rule page
        $catalogRulePage = Factory::getPageFactory()->getCatalogRulePromoCatalog();
        $catalogRulePage->open();

        // Add new Catalog Price Rule
        $pageActionsBlock = $catalogRulePage->getPageActionsBlock();
        $pageActionsBlock->clickAddNew();

        // Fill and Save the Form
        $catalogRuleCreatePage = Factory::getPageFactory()->getCatalogRulePromoCatalogNew();
        $newCatalogRuleForm = $catalogRuleCreatePage->getCatalogPriceRuleForm();
        $catalogRuleFixture = Factory::getFixtureFactory()->getMagentoCatalogRuleCatalogPriceRule();
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
        $this->assertTrue($gridRow->isVisible(),
                'Rule name "' . $catalogRuleFixture->getRuleName() . '" not found in the grid');
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
     * This method verifies information on the storefront.
     * @param Fixture\Product $product
     */
    protected function verifyPriceRules(Fixture\Product $product) {
        // open the front end home page of the store
        $frontendHomePage = Factory::getPageFactory()->getCmsIndexIndex();
        $frontendHomePage->open();
        // open the category associated with the product
        $frontendHomePage->getTopmenu()->selectCategoryByName($product->getCategoryName());
        // verify the product is displayed in the category
        $categoryPage = Factory::getPageFactory()->getCatalogCategoryView();
        $productListBlock = $categoryPage->getListProductBlock();
        $this->assertTrue($productListBlock->isProductVisible($product->getProductName()));
    }
}
