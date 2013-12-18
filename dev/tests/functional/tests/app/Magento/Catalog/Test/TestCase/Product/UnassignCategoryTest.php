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
use Magento\Catalog\Test\Fixture\Product;

/**
 * Unassign product from category on Product page
 *
 * @package Magento\Catalog\Test\TestCase\Product
 */
class UnassignCategoryTest extends Functional
{
    /**
     * Login into backend area before test
     */
    protected function setUp()
    {
        Factory::getApp()->magentoBackendLoginUser();
    }

    /**
     * Unassigning products from the category on Product Information page
     *
     * @ZephyrId MAGETWO-12417
     */
    public function testUnassignOnProductPage()
    {
        //Data
        $simple = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct();
        $simple->switchData('simple');
        $simple->persist();
        //Steps
        $editProductPage = Factory::getPageFactory()->getCatalogProductEdit();
        $editProductPage->open(array('id' => $simple->getProductId()));
        $editProductPage->getProductBlockForm()->clearCategorySelect();
        $editProductPage->getProductBlockForm()->save($simple);
        //Verifying
        $editProductPage->getMessagesBlock()->assertSuccessMessage();
        //Flush cache
        $cachePage = Factory::getPageFactory()->getAdminCache();
        $cachePage->open();
        $cachePage->getActionsBlock()->flushMagentoCache();
        $cachePage->getMessagesBlock()->assertSuccessMessage();
        //Verifying
        $this->assertAbsenceOnCategory($simple);
    }

    /**
     * Assert absence product on category page (frontend)
     *
     * @param Product $product
     */
    protected function assertAbsenceOnCategory($product)
    {
        //Pages
        $frontendHomePage = Factory::getPageFactory()->getCmsIndexIndex();
        $categoryPage = Factory::getPageFactory()->getCatalogCategoryView();
        //Steps
        $frontendHomePage->open();
        $frontendHomePage->getTopmenu()->selectCategoryByName($product->getCategoryName());
        //Verification on category product list
        $productListBlock = $categoryPage->getListProductBlock();
        $this->assertFalse($productListBlock->isProductVisible($product->getProductName()));
    }
}
