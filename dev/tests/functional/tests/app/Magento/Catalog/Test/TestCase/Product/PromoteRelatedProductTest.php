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

namespace Magento\Catalog\Test\TestCase\Product;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Mtf\Fixture;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\FormTabs;
use Magento\Catalog\Test\Block\Product\Configurable\AffectedAttributeSet;
use Magento\Catalog\Test\Fixture\Product;

/**
 * Create simple product for BAT
 *
 * @package Magento\Catalog\Test\TestCase\Product
 */
class PromoteRelatedProductTest extends Functional
{
    /**
     * Login into backend area before test
     */
    protected function setUp()
    {
        Factory::getApp()->magentoBackendLoginUser();
    }

    /**
     * Promoting products as related
     *
     * @ZephyrId MAGETWO-12392
     */
    public function testCreateProductAdvancedInventory()
    {

        // Setup preconditions for MAGETWO-12392

        // simple product 1
        $simpleProduct1 = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $simpleProduct1->persist();
        // simple product 2
        $simpleProduct2 = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $simpleProduct2->persist();

        /*
        // configurable product
        $configurableFixture = Factory::getFixtureFactory()->getMagentoCatalogConfigurableProduct();
        $configurableFixture->persist();
        */

        // Flush cache
        $cachePage = Factory::getPageFactory()->getAdminCache();
        $cachePage->open();
        $cachePage->getActionsBlock()->flushMagentoCache();


        // Steps
        $productEditPage = Factory::getPageFactory()->getCatalogProductEdit();
        $productEditPage->open(array('id' => $simpleProduct1->getProductId()));

        $this->directToRelatedProductPage($productEditPage);
        $this->addRelatedProduct($productEditPage, $simpleProduct1, $simpleProduct2);
    }

    /**
     * @param \Magento\Catalog\Test\Page\Product\CatalogProductEdit $productEditPage
     */
    protected function directToRelatedProductPage($productEditPage)
    {
        $productBlockForm = $productEditPage->getProductBlockForm();
        /**
         * Open tab "Advanced Settings" to make all nested tabs visible and available to interact
         */
        $productBlockForm->getRootElement()
            ->find('ui-accordion-product_info_tabs-advanced-header-0', Locator::SELECTOR_ID)->click();

        /**
         * comments here
         */
        $productBlockForm->waitForElementVisible('product_info_tabs_related', Locator::SELECTOR_ID);
        $productBlockForm->getRootElement()
            ->find('product_info_tabs_related', Locator::SELECTOR_ID)->click();

        /**
         * comments here
         */
        //$productBlockForm->waitForElementVisible('[title="Reset Filter"][class*=action]', Locator::SELECTOR_CSS);
        //$productBlockForm->getRootElement()
            //->find('[title="Reset Filter"][class*=action]', Locator::SELECTOR_CSS)->click();
    }

    /**
     * @param \Magento\Catalog\Test\Page\Product\CatalogProductEdit $productEditPage
     * @param \Magento\Catalog\Test\Fixture\Product $product
     * @param \Magento\Catalog\Test\Fixture\Product $relatedProduct1
     * @param \Magento\Catalog\Test\Fixture\Product $relatedProduct2
     */
    protected function addRelatedProduct($productEditPage, $product, $relatedProduct1, $relatedProduct2=null)
    {
        $productEditPage->getProductBlockForm()
            ->waitForElementVisible('[title="Reset Filter"][class*=action]', Locator::SELECTOR_CSS);
        $productEditPage->getProductEditGrid()->searchAndSelect(array('name' => $relatedProduct1->getProductName()));
        $productEditPage->getProductBlockForm()->save($product);
        //Verifying
        $this->assertSuccessMessage("You saved the product.", $productEditPage);
    }

    /**
     * @param $messageText
     * @param \Magento\Catalog\Test\Page\Product\CatalogProductEdit $productEditPage
     */
    protected function assertSuccessMessage($messageText, $productEditPage)
    {
        $messageBlock = $productEditPage->getMessagesBlock();
        $this->assertContains(
            $messageText,
            $messageBlock->getSuccessMessages(),
            sprintf('Message "%s" is not appear.', $messageText)
        );
    }
}
