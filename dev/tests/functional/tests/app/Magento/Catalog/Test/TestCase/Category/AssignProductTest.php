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

namespace Magento\Catalog\Test\TestCase\Category;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Catalog\Test\Fixture\Category;
use Magento\Catalog\Test\Fixture\Product;

/**
 * Class AssignProducts
 *
 * @package Magento\Catalog\Test\TestCase\Category
 */
class AssignProductTest extends Functional
{
    /**
     * Creating a subcategory and assign products to the category
     *
     * @ZephyrId MAGETWO-16351
     */
    public function testAssignProducts()
    {
        //Data
        $simple = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct();
        $simple->switchData('simple_required');
        $simple->persist();
        $configurable = Factory::getFixtureFactory()->getMagentoCatalogConfigurableProduct();
        $configurable->switchData('configurable_required');
        $configurable->persist();
        $bundle = Factory::getFixtureFactory()->getMagentoBundleBundleFixed();
        $bundle->switchData('bundle_fixed_required');
        $bundle->persist();
        $category = Factory::getFixtureFactory()->getMagentoCatalogCategory();
        $category->switchData('default');
        $category->persist();
        //Pages & Blocks
        $catalogCategoryPage = Factory::getPageFactory()->getCatalogCategory();
        $treeBlock = $catalogCategoryPage->getTreeBlock();
        $formBlock = $catalogCategoryPage->getFormBlock();
        $messageBlock = $catalogCategoryPage->getMessageBlock();
        //Steps
        Factory::getApp()->magentoBackendLoginUser();
        $catalogCategoryPage->open();
        $treeBlock->selectCategory($category->getCategoryPath() . '/' . $category->getCategoryName());
        $formBlock->openCategoryProductsTab();
        $categoryProductsGrid = $formBlock->getCategoryProductsGrid();
        $products = [$simple, $configurable, $bundle];
        /** @var Product $product */
        foreach($products as $product) {
            $categoryProductsGrid->searchAndSelect(['sku' => $product->getProductSku()]);
        }
        $formBlock->save($category);
        $messageBlock->assertSuccessMessage();
        //Clean Cache
        $cachePage = Factory::getPageFactory()->getAdminCache();
        $cachePage->open();
        $cachePage->getActionsBlock()->flushMagentoCache();
        $cachePage->getMessagesBlock()->assertSuccessMessage();
        //Indexing
        $indexPage = Factory::getPageFactory()->getAdminProcessList();
        $indexPage->open();
        $indexPage->getActionsBlock()->reindexAll();
        $indexPage->getMessagesBlock()->assertSuccessMessage();
        //Verifying
        $this->assertProductsOnCategory($category, $products);
    }

    /**
     * Verifying that category present in Frontend with products
     *
     * @param Category $category
     * @param array $products
     */
    protected function assertProductsOnCategory(Category $category, array $products)
    {
        //Open created category on frontend
        $frontendHomePage = Factory::getPageFactory()->getCmsIndexIndex();
        $categoryPage = Factory::getPageFactory()->getCatalogCategoryView();
        $frontendHomePage->open();
        $navigationMenu = $frontendHomePage->getTopmenu();
        $navigationMenu->selectCategoryByName($category->getCategoryName());
        $this->assertEquals($category->getCategoryName(), $frontendHomePage->getTitleBlock()->getTitle());
        $productListBlock = $categoryPage->getListProductBlock();
        /** @var Product $product */
        foreach ($products as $product) {
            $this->assertTrue(
                $productListBlock->isProductVisible($product->getProductName()),
                'Product is absent on category page.'
            );
        }
    }
}