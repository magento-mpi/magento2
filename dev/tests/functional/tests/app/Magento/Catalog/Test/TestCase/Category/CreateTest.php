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

/**
 * Class CreateTest
 * Create category test
 *
 * @package Magento\Catalog\Test\TestCase\Category
 */
class CreateTest extends Functional
{
    /**
     * Creating Category from Category page with required fields only
     *
     * @ZephyrId MAGETWO-12513
     */
    public function testWithRequiredFields()
    {
        //Data
        /** @var Category $category */
        $category = Factory::getFixtureFactory()->getMagentoCatalogCategory();
        //Pages & Blocks
        $catalogCategoryPage = Factory::getPageFactory()->getCatalogCategory();
        $treeBlock = $catalogCategoryPage->getTreeBlock();
        $formBlock = $catalogCategoryPage->getFormBlock();
        $messageBlock = $catalogCategoryPage->getMessageBlock();
        //Steps
        Factory::getApp()->magentoBackendLoginUser();
        $catalogCategoryPage->open();
        $treeBlock->selectCategory($category->getCategoryPath());
        $treeBlock->addSubcategory();
        $formBlock->fill($category);
        $formBlock->save($category);
        //Verifying
        $messageBlock->assertSuccessMessage();
        //Flush cache
        $cachePage = Factory::getPageFactory()->getAdminCache();
        $cachePage->open();
        $cachePage->getActionsBlock()->flushMagentoCache();
        $cachePage->getMessagesBlock()->assertSuccessMessage();
        //Verifying
        $this->assertCategoryOnFrontend($category);
    }

    /**
     * Verify category on the frontend
     *
     * @param Category $category
     */
    protected function assertCategoryOnFrontend(Category $category)
    {
        //Open created category on frontend
        $frontendHomePage = Factory::getPageFactory()->getCmsIndexIndex();
        $frontendHomePage->open();
        $navigationMenu = $frontendHomePage->getTopmenu();
        $navigationMenu->selectCategoryByName($category->getCategoryName());
        $this->assertEquals($category->getCategoryName(), $frontendHomePage->getTitleBlock()->getTitle());
    }
}
