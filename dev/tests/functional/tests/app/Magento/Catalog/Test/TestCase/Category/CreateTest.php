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
 * Create new subcategory test
 *
 * @package Magento\Catalog\Test\TestCase\Category
 */
class CreateTest extends Functional
{
    /**
     * Category create
     */
    public function testCreateCategory()
    {
        //Data
        /** @var Category $category */
        $category = Factory::getFixtureFactory()->getMagentoCatalogCategory()->switchData('subcategory');
        //Pages & Blocks
        $catalogCategoryPage = Factory::getPageFactory()->getAdminCatalogCategory();
        $treeBlock = $catalogCategoryPage->getTreeBlock();
        $formBlock = $catalogCategoryPage->getFormBlock();
        $messageBlock = $catalogCategoryPage->getMessageBlock();
        $loader = $catalogCategoryPage->getTemplateBlock();
        //Steps
        Factory::getApp()->magentoBackendLoginUser();
        $catalogCategoryPage->open();
        $loader->waitLoader();
        $treeBlock->selectDefaultCategory();
        $loader->waitLoader();
        $treeBlock->addSubcategory();
        $loader->waitLoader();
        $formBlock->fill($category);
        $formBlock->save($category);
        //Verifying
        $messageBlock->waitForSuccessMessage($category);

        //Open created category on frontend
        $frontendHomePage = Factory::getPageFactory()->getCmsIndexIndex();
        $frontendHomePage->open();
        $navigationMenu = $frontendHomePage->getTopmenu();
        $navigationMenu->selectCategoryByName($category->getCategoryName());
        $pageTitleBlock = $frontendHomePage->getTitleBlock();
        $categoryTitle = $pageTitleBlock->getTitle();

        $this->assertEquals($category->getCategoryName(), $categoryTitle);
    }
}
