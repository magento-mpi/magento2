<?php
/**
 * {license_notice}
 *
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
        $catalogCategoryEditPage = Factory::getPageFactory()->getCatalogCategoryEditId();
        $treeBlockEdit = $catalogCategoryEditPage->getTreeBlock();
        $formBlock = $catalogCategoryEditPage->getFormBlock();
        $actionsBlock = $catalogCategoryEditPage->getPageActionsBlock();
        $messagesBlock = $catalogCategoryEditPage->getMessagesBlock();
        //Steps
        Factory::getApp()->magentoBackendLoginUser();
        $catalogCategoryPage->open();
        $treeBlock->selectCategory($category, false);
        $treeBlockEdit->addSubcategory();
        $formBlock->fill($category);
        $actionsBlock->save();
        //Verifying
        $messagesBlock->assertSuccessMessage();
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
